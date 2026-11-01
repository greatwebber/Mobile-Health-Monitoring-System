# Progressive Web Application Audit

**Application:** EdeHealth Sentinel  
**Audit date:** 1 November 2026  
**Environment:** Local XAMPP/Apache and Laravel development server  
**Result:** Automated foundation checks passed; representative-browser installation remains pending

## Scope

This audit evaluates the installability metadata, application entry point, service-worker strategy, offline fallback, update behavior, sensitive-data caching boundary and approved offline synchronization workflow. It does not constitute clinical validation, a penetration test or evidence of operation on physical target devices.

## Results

| Control | Result | Evidence |
|---|---|---|
| Application entry point | Pass | The XAMPP URL returned HTTP 200 after Laravel runtime directories were made writable by the local Apache user. |
| Web app manifest | Pass | Valid JSON with relative ID, start URL and scope; standalone display; language, direction, theme/background colors and application identity are declared. |
| Application icons | Pass | 192×192 and 512×512 PNG icons are present in both `any` and `maskable` variants, and their actual dimensions match the manifest. |
| Manifest delivery | Pass | Apache returned HTTP 200 with `application/manifest+json`. |
| Service-worker delivery | Pass | Apache returned HTTP 200 with JavaScript content type and explicit no-cache/revalidation headers. |
| Service-worker update path | Pass | The static cache is versioned as `edehealth-static-v3`; activation removes obsolete EdeHealth caches. A waiting update presents an accessible notice and activates only after the user chooses **Update now**, preventing unexpected first-install reloads or interruption of unsaved forms. |
| Offline navigation | Pass | Navigation is network-first and falls back to a self-contained branded offline page with retry and urgent-care guidance. |
| Static asset caching | Pass | Runtime caching is restricted to same-origin scripts, styles, images and fonts under versioned build or icon paths. |
| Sensitive response boundary | Pass | Authenticated pages, clinical endpoints and draft payloads are not precached or written to service-worker caches. |
| Offline draft protection | Pass | The approved profile draft uses IndexedDB with a non-extractable AES-GCM key and remains visibly queued until confirmed by the server. |
| Synchronization safety | Pass | Authentication, server validation, UUID idempotency receipts and version-conflict refusal are covered by feature tests. |
| Logout cleanup | Pass | Explicit logout removes the current user's encrypted draft and browser key. |
| Automated regression | Pass | `php artisan test`: 113 tests passed with 648 assertions. |
| Production assets | Pass | `npm run build` completed successfully. |
| Code style | Pass | `vendor/bin/pint --test` completed successfully. |
| Dependency advisories | Pass | Composer and production npm audits reported no known vulnerabilities. |

## HTTP Verification

The following resources were requested from the real XAMPP application path:

| Resource | Status | Relevant response |
|---|---:|---|
| Application start URL | 200 | `text/html; charset=utf-8`, private/no-cache response |
| `manifest.webmanifest` | 200 | `application/manifest+json` |
| `service-worker.js` | 200 | JavaScript content type; no-cache/no-store/must-revalidate |
| `offline.html` | 200 | `text/html` |

The first XAMPP application request exposed a local environment issue: Apache could read the SQLite database but could not update database-backed sessions. Write access was granted to Laravel's local runtime paths (`database`, `storage` and `bootstrap/cache`), after which the start URL returned HTTP 200. Deployment must grant only the web-server identity the minimum required write access; production must not use broad public write permissions.

## Open Verification

Browser automation was not available in this workspace session. Therefore, the following claims are deliberately not marked as passed:

- browser-provided installation prompt and successful installation;
- standalone launch from an installed icon;
- service-worker lifecycle and offline fallback observed in browser developer tools;
- encrypted draft recovery after a real browser restart;
- Android, iOS/iPadOS and desktop responsive interaction;
- browser accessibility and performance audits.

These checks are defined in [DEVICE_COMPATIBILITY.md](DEVICE_COMPATIBILITY.md) and must be completed before Phase 6 can leave **In progress**.

## Conclusion

No unresolved critical issue was found in the automated PWA foundation or HTTP-delivery checks. The application has a technically valid, privacy-conscious PWA baseline. Release acceptance remains conditional on the representative-browser and physical-device evidence listed above.
