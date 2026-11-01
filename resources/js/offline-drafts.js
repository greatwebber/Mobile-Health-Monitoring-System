const DATABASE_NAME = 'edehealth-private-drafts-v1';
const DATABASE_VERSION = 1;
const KEY_STORE = 'keys';
const DRAFT_STORE = 'drafts';
const SUPPORTED = 'indexedDB' in window && window.crypto?.subtle;

function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DATABASE_NAME, DATABASE_VERSION);

        request.onupgradeneeded = () => {
            const database = request.result;
            if (!database.objectStoreNames.contains(KEY_STORE)) database.createObjectStore(KEY_STORE, { keyPath: 'owner' });
            if (!database.objectStoreNames.contains(DRAFT_STORE)) database.createObjectStore(DRAFT_STORE, { keyPath: 'id' });
        };
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

async function storeRequest(storeName, mode, operation) {
    const database = await openDatabase();

    return new Promise((resolve, reject) => {
        const transaction = database.transaction(storeName, mode);
        const request = operation(transaction.objectStore(storeName));
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
        transaction.oncomplete = () => database.close();
        transaction.onerror = () => reject(transaction.error);
    });
}

async function encryptionKey(owner) {
    const stored = await storeRequest(KEY_STORE, 'readonly', (store) => store.get(owner));
    if (stored?.key) return stored.key;

    const key = await crypto.subtle.generateKey({ name: 'AES-GCM', length: 256 }, false, ['encrypt', 'decrypt']);
    await storeRequest(KEY_STORE, 'readwrite', (store) => store.put({ owner, key }));
    return key;
}

async function encrypt(owner, value) {
    const key = await encryptionKey(owner);
    const iv = crypto.getRandomValues(new Uint8Array(12));
    const plaintext = new TextEncoder().encode(JSON.stringify(value));
    const ciphertext = await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, key, plaintext);

    return { ciphertext, iv: Array.from(iv) };
}

async function decrypt(owner, record) {
    const key = await encryptionKey(owner);
    const plaintext = await crypto.subtle.decrypt(
        { name: 'AES-GCM', iv: new Uint8Array(record.iv) },
        key,
        record.ciphertext,
    );

    return JSON.parse(new TextDecoder().decode(plaintext));
}

function draftKey(form) {
    return `${form.dataset.offlineOwner}:${form.dataset.offlineDraft}`;
}

function formPayload(form) {
    const payload = {};

    for (const [name, value] of new FormData(form).entries()) {
        if (!name.startsWith('_') && typeof value === 'string') payload[name] = value;
    }

    return payload;
}

async function saveDraft(form, state = 'local') {
    const owner = form.dataset.offlineOwner;
    const id = draftKey(form);
    const current = await storeRequest(DRAFT_STORE, 'readonly', (store) => store.get(id));
    const draftId = current?.draftId ?? crypto.randomUUID();
    const encrypted = await encrypt(owner, {
        baseVersion: form.dataset.offlineBaseVersion,
        draftId,
        payload: formPayload(form),
    });

    const record = {
        id,
        owner,
        type: form.dataset.offlineDraft,
        baseVersion: form.dataset.offlineBaseVersion,
        draftId,
        state,
        retryCount: current?.retryCount ?? 0,
        updatedAt: new Date().toISOString(),
        ...encrypted,
    };

    await storeRequest(DRAFT_STORE, 'readwrite', (store) => store.put(record));
    return record;
}

async function updateDraftState(record, state) {
    await storeRequest(DRAFT_STORE, 'readwrite', (store) => store.put({
        ...record,
        state,
        retryCount: (record.retryCount ?? 0) + (state === 'queued' ? 1 : 0),
        updatedAt: new Date().toISOString(),
    }));
}

function panelElements() {
    const panel = document.querySelector('[data-offline-draft-panel]');
    return {
        panel,
        title: panel?.querySelector('[data-offline-draft-title]'),
        message: panel?.querySelector('[data-offline-draft-message]'),
    };
}

function showDraftStatus(title, message, tone = 'sky') {
    const elements = panelElements();
    if (!elements.panel) return;

    elements.panel.hidden = false;
    elements.title.textContent = title;
    elements.message.textContent = message;
    elements.panel.dataset.tone = tone;
}

function hideDraftStatus() {
    const { panel } = panelElements();
    if (panel) panel.hidden = true;
}

function restorePayload(form, payload) {
    Object.entries(payload).forEach(([name, value]) => {
        const field = form.elements.namedItem(name);
        if (field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement) {
            field.value = value;
        }
    });
}

async function synchronize(form, record) {
    if (!navigator.onLine || record.state !== 'queued') return;

    showDraftStatus('Synchronizing securely', 'The queued draft is being validated by the server. It is not confirmed yet.');

    try {
        const decrypted = await decrypt(record.owner, record);
        const response = await fetch(form.dataset.offlineSyncUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({
                ...decrypted.payload,
                base_updated_at: decrypted.baseVersion,
                draft_id: decrypted.draftId,
            }),
        });

        if (response.ok) {
            await storeRequest(DRAFT_STORE, 'readwrite', (store) => store.delete(record.id));
            showDraftStatus('Synchronization complete', 'The server validated and saved this profile draft.');
            window.setTimeout(() => window.Turbo?.visit(window.location.href, { action: 'replace' }), 500);
            return;
        }

        if (response.status === 409) {
            await updateDraftState(record, 'conflict');
            showDraftStatus('Review required', 'The server profile changed after this draft began. The newer server record was not overwritten.', 'amber');
            return;
        }

        if ([401, 403, 419].includes(response.status)) {
            await updateDraftState(record, 'needs_authentication');
            showDraftStatus('Sign-in required', 'This draft remains encrypted on this device. Sign in again before synchronizing.', 'amber');
            return;
        }

        if (response.status === 422) {
            const result = await response.json();
            const fields = Object.keys(result.errors ?? {}).join(', ');
            await updateDraftState(record, 'needs_review');
            showDraftStatus('Draft needs review', `The server rejected ${fields || 'one or more fields'}. Update the form and submit again.`, 'amber');
            return;
        }

        throw new Error(`Unexpected synchronization response: ${response.status}`);
    } catch (error) {
        await updateDraftState(record, 'queued');
        showDraftStatus('Synchronization paused', 'The connection was interrupted. The encrypted draft remains queued for another retry.', 'amber');
        console.error('Offline draft synchronization failed.', error);
    }
}

async function initializeDraftForm() {
    const form = document.querySelector('form[data-offline-draft]');
    if (!form || form.dataset.offlineInitialized || !SUPPORTED) return;

    form.dataset.offlineInitialized = 'true';
    const saveButton = form.querySelector('[data-offline-draft-save]');
    if (saveButton) saveButton.hidden = false;
    const id = draftKey(form);
    const record = await storeRequest(DRAFT_STORE, 'readonly', (store) => store.get(id));

    if (record) {
        if (record.baseVersion !== form.dataset.offlineBaseVersion || record.state === 'conflict') {
            showDraftStatus('Newer server record detected', 'An older encrypted draft is retained but was not restored. Discard it before starting from the current server profile.', 'amber');
        } else {
            try {
                const decrypted = await decrypt(record.owner, record);
                restorePayload(form, decrypted.payload);
                showDraftStatus(
                    record.state === 'queued' ? 'Draft queued' : 'Encrypted draft restored',
                    record.state === 'queued'
                        ? 'This change is not confirmed. It will synchronize when a secure connection is available.'
                        : 'Unsaved changes were restored from encrypted browser storage.',
                );
                if (record.state === 'queued' && navigator.onLine) await synchronize(form, record);
            } catch (error) {
                showDraftStatus('Draft cannot be opened', 'The local encryption key is unavailable or the draft is damaged. Discard it to continue safely.', 'amber');
                console.error('Encrypted draft restoration failed.', error);
            }
        }
    }

    let autosaveTimer;
    form.addEventListener('input', () => {
        window.clearTimeout(autosaveTimer);
        autosaveTimer = window.setTimeout(async () => {
            await saveDraft(form);
            showDraftStatus('Encrypted draft saved', 'Changes are protected in this browser but have not been submitted to the server.');
        }, 600);
    });

    form.querySelector('[data-offline-draft-save]')?.addEventListener('click', async () => {
        await saveDraft(form);
        showDraftStatus('Encrypted draft saved', 'Changes are protected in this browser but have not been submitted to the server.');
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const queued = await saveDraft(form, 'queued');

        if (!navigator.onLine) {
            showDraftStatus('Draft queued—not confirmed', 'Reconnect and remain signed in so the server can validate and save this change.', 'amber');
            return;
        }

        await synchronize(form, queued);
    });

    document.querySelector('[data-offline-draft-discard]')?.addEventListener('click', async () => {
        await storeRequest(DRAFT_STORE, 'readwrite', (store) => store.delete(id));
        hideDraftStatus();
    });
}

async function clearPrivateDrafts(owner) {
    const database = await openDatabase();

    await new Promise((resolve, reject) => {
        const transaction = database.transaction([DRAFT_STORE, KEY_STORE], 'readwrite');
        transaction.objectStore(DRAFT_STORE).delete(`${owner}:patient-profile`);
        transaction.objectStore(KEY_STORE).delete(owner);
        transaction.oncomplete = resolve;
        transaction.onerror = () => reject(transaction.error);
    });

    database.close();
}

document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.clearPrivateDrafts || form.dataset.privateDraftsCleared) return;

    event.preventDefault();
    const owner = document.querySelector('meta[name="current-user-id"]')?.content;
    if (SUPPORTED && owner) await clearPrivateDrafts(owner);
    form.dataset.privateDraftsCleared = 'true';
    form.requestSubmit();
}, true);

window.addEventListener('online', async () => {
    const form = document.querySelector('form[data-offline-draft]');
    if (!form || !SUPPORTED) return;
    const record = await storeRequest(DRAFT_STORE, 'readonly', (store) => store.get(draftKey(form)));
    if (record?.state === 'queued') await synchronize(form, record);
});

document.addEventListener('turbo:load', initializeDraftForm);
