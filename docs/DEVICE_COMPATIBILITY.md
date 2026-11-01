# Device and Browser Compatibility Record

**Application:** EdeHealth Sentinel  
**Record opened:** 1 November 2026  
**Status:** Manual representative-device verification pending

This record separates verified technical checks from device claims that still require direct evidence. Use fictional demonstration data only. Do not enter real patient information during compatibility testing.

## Target Matrix

| Target | Minimum scenario | Status | Evidence / notes |
|---|---|---|---|
| XAMPP/Apache on local desktop | Start URL, manifest, service worker and offline page return correctly | Pass | HTTP checks completed on 1 November 2026. |
| Android phone, current Chrome | Install, standalone launch, navigation, profile draft offline/online recovery and logout cleanup | Pending | Physical or connected browser session required. |
| iPhone/iPad, current Safari | Add to Home Screen, standalone launch, safe-area layout, navigation and offline fallback | Pending | Confirm iOS installation wording and storage behavior; do not assume Chromium behavior. |
| Desktop Chrome or Edge | Install, standalone window, keyboard navigation, online/offline transition and application update | Pending | Connected browser session required. |
| Small phone viewport (320–375 px) | No horizontal overflow; touch targets, bottom navigation, forms and dialogs remain usable | Pending | Test patient and staff roles. |
| Tablet viewport (768–1024 px) | Layout, charts, tables and navigation adapt without lost actions | Pending | Test portrait and landscape. |
| Low-bandwidth/intermittent network | Loader/status states remain clear; navigation failure reaches offline page; queued draft does not duplicate | Pending | Use network throttling and one forced interruption. |
| Keyboard and assistive technology | Logical focus order, visible focus, landmarks, labels, status announcements and reduced motion | Pending | Include one desktop screen-reader pass. |

## Test Procedure

For each target, record the device, operating-system version, browser/version, date, tester and result.

1. Clear prior EdeHealth site data and open the deployed HTTPS URL, or a permitted localhost URL during development.
2. Confirm the landing page loads, the manifest is detected and no service-worker registration error appears.
3. Install or add the application to the home screen, then launch it from the installed icon.
4. Confirm standalone presentation, theme color, application icon and safe-area layout.
5. Sign in with the fictional patient account and navigate through the bottom dock. Confirm Turbo transitions do not produce a full-page flash and delayed requests expose the accessible loader.
6. Open the patient profile, change a permitted fictional value, disconnect the network and save the draft. Confirm it is labelled queued and unconfirmed.
7. Close and reopen the application. Confirm the protected draft can be recovered in the same browser profile without appearing in Cache Storage.
8. Restore connectivity and synchronize once. Confirm one server update occurs and the queue clears. Repeat the same sync request and confirm no duplicate change.
9. Create a server-side version conflict in a controlled test, reconnect and confirm the local draft is preserved instead of silently overwriting the server.
10. Disconnect again and navigate to an uncached page. Confirm the branded offline page appears and its retry action works after reconnection.
11. Log out. Confirm the encrypted profile draft and its browser key are removed, then verify protected pages cannot be reopened from history.
12. Repeat relevant navigation and layout checks with fictional nurse, clinician and administrator accounts.

## Acceptance Rules

A target passes only when all required scenarios complete without exposure of clinical responses in Cache Storage, silent data loss, duplicate synchronization, inaccessible critical controls or misleading completion states. Capture screenshots of installation, standalone launch, queued draft, offline fallback and successful recovery, with all identifiers fictional or redacted.

Any failure affecting privacy, authentication, synchronization integrity, installation or access to urgent-care guidance is release-blocking. Cosmetic issues may be recorded separately with severity and an owner.

## Execution Log

| Date | Target and version | Tester | Result | Evidence / issue reference |
|---|---|---|---|---|
| 2026-11-01 | XAMPP Apache 2.4.56 / PHP 8.2.4 HTTP delivery | Development team | Pass | Start URL and all public PWA resources returned HTTP 200 after local runtime permissions were corrected. |

