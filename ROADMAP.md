# Mobile Health Monitoring System — Development Roadmap

## Project Title

**Development of a Progressive Web-Based Mobile Health Monitoring System with AI-Based Disease Risk Prediction: A Case Study of the Medical Centre, Federal Polytechnic Ede**

## Document Information

| Item | Details |
|---|---|
| Project type | Academic research and software development project |
| Product form | Installable Progressive Web Application (PWA) |
| Case study | Medical Centre, Federal Polytechnic Ede |
| Primary users | Patients, nurses, clinicians, doctors and system administrators |
| Proposed core stack | Laravel, PHP, MySQL, Blade/Bootstrap, JavaScript, Chart.js and Python/FastAPI |
| AI scope | One clearly defined disease-risk prediction model for the first release |
| Estimated delivery period | 16–20 weeks |
| Document status | Living implementation roadmap |

---

## Roadmap Tracking Guide

This document is also the project's living implementation tracker. Update it whenever work is started, completed, reviewed or blocked.

### Task Markers

- `[ ]` — Not started
- `[~]` — In progress
- `[x]` — Completed and verified
- `[!]` — Blocked; explain the blocker beside the item
- `[-]` — Removed from the approved scope; explain why

> Use `[x]` only after the work has been tested or its deliverable has been reviewed. Starting a task should change its marker to `[~]`, not `[x]`.

### Phase Status Definitions

| Status | Meaning |
|---|---|
| Not started | No implementation task in the phase has begun |
| In progress | At least one task has begun, but the exit criteria are not yet satisfied |
| Blocked | Progress cannot continue until a recorded dependency or decision is resolved |
| Ready for review | Planned work is complete and awaits stakeholder or technical review |
| Completed | Deliverables are verified and all exit criteria are satisfied |

### Progress Dashboard

| Phase | Planned period | Status | Completion date | Notes |
|---|---:|---|---|---|
| 1. Discovery and Scope | Weeks 1–2 | Not started | — | — |
| 2. Ethics, Privacy and Data Planning | Weeks 2–3 | Not started | — | — |
| 3. UX, PWA Design and Architecture | Weeks 3–4 | In progress | — | Original mobile-first shell, role-aware bottom navigation and reload-free Turbo navigation implemented; formal wireframes and stakeholder review pending |
| 4. Engineering Foundation | Weeks 4–6 | In progress | — | Laravel, authentication, account roles, tests and PWA shell implemented |
| 5. Clinical Monitoring MVP | Weeks 6–9 | In progress | — | Core clinical records, longitudinal timeline and verified vital-sign trend charts implemented; threshold alerts are next |
| 6. Offline-Aware PWA Completion | Weeks 8–10 | In progress | — | Safe static caching, offline fallback and connection status implemented; offline drafts pending |
| 7. AI Research Pipeline | Weeks 7–11 | Not started | — | — |
| 8. AI Service and Product Integration | Weeks 11–13 | Not started | — | — |
| 9. Verification and Hardening | Weeks 13–16 | Not started | — | — |
| 10. Pilot Evaluation | Weeks 16–18 | Not started | — | — |
| 11. Deployment and Academic Handover | Weeks 18–20 | Not started | — | — |

### Update Record

Add one row for every meaningful roadmap update.

| Date | Phase | Update | Decision, evidence or blocker | Updated by |
|---|---|---|---|---|
| 2026-10-31 | — | Roadmap created | Initial scope and delivery plan documented | Project team |
| 2026-10-31 | 4 and 6 | Engineering implementation started | Laravel 12 and Git initialized; authentication, patient-default roles, responsive product shell, PWA manifest, icons, service worker and offline fallback verified by build, HTTP checks and 31 automated tests | Development team |
| 2026-10-31 | 4 and 5 | Demo data and clinical profiles implemented | Four environment-gated demo accounts, one fictional patient profile and three staff profiles seeded idempotently; profile validation and least-privilege policies verified by 39 automated tests | Development team |
| 2026-10-31 | 3 and 4 | Mobile PWA navigation redesigned | Authenticated experience rebuilt with a safe-area-aware bottom dock, role-specific patient/staff menus, raised primary action, compact app bar, redesigned dashboard and aligned account screens; verified by 42 automated tests | Development team |
| 2026-10-31 | 2 and 5 | Consent and medical-history foundation implemented | Patient consent decisions are versioned and append-only; structured medical history supports patient updates and clinical read access under least-privilege policies. Registration and demo seeders initialize the records; migrations, repeat seeding, production asset build, dependency audits and 52 automated tests with 236 assertions passed | Development team |
| 2026-10-31 | 5 | Clinical encounter workflow implemented | Nurses and clinicians can open, search, document, assign and complete encounters; patients can review only their own visit history and completed records are immutable. Navigation, fictional seed data, production build, repeat seeding, dependency audits and 60 automated tests with 285 assertions passed | Development team |
| 2026-10-31 | 5 | Structured symptoms and vital signs implemented | Authorized staff can append timestamped, source-labelled and verified symptoms and vital-sign snapshots to active encounters; patients can view observations only through their own visits and final encounters reject new data. Validation, fictional seed observations, production build, dependency audits and 66 automated tests with 327 assertions passed | Development team |
| 2026-10-31 | 5 | Laboratory and relevant clinical results implemented | Authorized staff can append verified numeric or qualitative results with category, unit, reference range, specimen, timestamps and source-reported flags; patients can review only results belonging to their visits. No flags are inferred by the system. Migration, repeat seeding, production build, audits and 72 automated tests with 357 assertions passed | Development team |
| 2026-10-31 | 3 and 6 | Delayed PWA navigation feedback implemented | The authenticated app bar now shows an accessible circular loader for internal page transitions and valid form submissions after a 180 ms anti-flicker delay; external, download, hash-only and new-tab links are excluded, browser restoration resets state, and reduced-motion preferences are respected. Production build, audits and 73 automated tests with 369 assertions passed | Development team |
| 2026-10-31 | 3, 4 and 6 | Reload-free Turbo navigation implemented | Same-origin links and forms now use Turbo Drive background requests and body replacement while preserving Laravel-rendered HTML and browser history. Sensitive page snapshot caching and hover prefetching are disabled, versioned asset changes force a controlled reload, and the custom app-bar loader follows Turbo lifecycle events. Production build, dependency audits and 74 automated tests with 378 assertions passed | Development team |
| 2026-10-31 | 5 | Longitudinal timeline and trend charts implemented | Patients can review their own combined visit, symptom, vital-sign and result history; nurses and clinicians can open an authorized patient timeline while administrators and unrelated patients are denied. Chart.js renders verified trends and is loaded only on demand for low-bandwidth PWA performance. Repeat seeding, production build, audits and 80 automated tests with 406 assertions passed | Development team |

---

## 1. Executive Summary

This project will deliver a secure, mobile-first Progressive Web Application for recording, monitoring and reviewing patients' health information at the Medical Centre, Federal Polytechnic Ede. The application will provide longitudinal monitoring of vital signs, symptoms, clinical encounters and relevant laboratory results. It will also integrate a versioned machine-learning model that estimates the risk of one clearly defined disease or clinical outcome.

The proposed system is a clinical decision-support and monitoring platform, not an autonomous diagnostic system. Every consequential AI result must be explainable, recorded and made available for review by an authorized healthcare professional. Clinical personnel will remain responsible for diagnosis, treatment and escalation decisions.

The PWA approach will allow the system to provide an app-like experience while remaining accessible through a web browser. Authorized users will be able to install it on supported phones, tablets and desktop computers. The interface will be designed for low-bandwidth conditions, responsive use and controlled offline operation.

---

## 2. Project Vision

To provide the Polytechnic community with an accessible and responsible digital-health platform that improves continuity of health monitoring, supports timely clinical review and demonstrates the safe use of artificial intelligence in disease-risk assessment.

## 3. Project Mission

To design, implement and evaluate a secure PWA that connects patient-entered and clinician-verified health information with longitudinal monitoring, configurable alerts and transparent AI-assisted risk assessment.

## 4. General Objective

To develop a secure Progressive Web Application for monitoring patient health information and providing clinician-supervised, AI-based disease-risk predictions at the Medical Centre, Federal Polytechnic Ede.

## 5. Specific Objectives

1. Study the existing patient registration, consultation, monitoring and follow-up processes at the Medical Centre.
2. Design a mobile-first PWA that supports patients and authorized medical personnel.
3. Implement secure management of patients, encounters, symptoms, vital signs, laboratory results and appointments.
4. Provide longitudinal charts and threshold-based alerts for selected health measurements.
5. Develop and evaluate a machine-learning model for one precisely defined disease or clinical outcome.
6. Integrate model predictions with explanations, model versioning and mandatory clinical review.
7. Protect sensitive health information through role-based access, auditing, consent management and secure data handling.
8. Evaluate the completed system for usability, functionality, security, performance, PWA compliance and predictive performance.

---

## 6. Product Identity and Unique Value Proposition

The system should not be presented as a generic hospital-management application. Its identity will be built around **continuous campus health monitoring, low-bandwidth PWA access and accountable AI assistance**.

### Proposed Product Name

**EdeHealth Sentinel**

The name is provisional and can be replaced after stakeholder review. “Sentinel” represents continuous, responsible monitoring rather than automatic diagnosis.

### Distinctive Features

- Installable PWA for phones, tablets and desktop devices.
- Mobile-first interface optimized for low-bandwidth campus environments.
- Offline capture of approved non-critical drafts with safe synchronization when connectivity returns.
- Clear distinction between patient-entered and clinician-verified observations.
- Longitudinal health timeline instead of isolated consultation records.
- Personalized health trend cards for supported measurements.
- Clinician-approved threshold alerts and escalation workflow.
- One clinically defined AI risk model with probability, risk band, important factors and limitations.
- Human review and feedback attached to every consequential prediction.
- Model-version history to make each result reproducible and auditable.
- Privacy-centered data export for authorized academic evaluation.
- PWA update notification so users know when a new application version is available.

### Proposed Design Direction

The visual identity should communicate calmness, safety and clinical clarity. It should use an original design system rather than copying a dashboard template unchanged.

- Primary palette: deep medical teal and clean neutral surfaces.
- Alert palette: accessible green, amber and red states with text/icons, never color alone.
- Typography: highly readable sans-serif type with a minimum comfortable mobile size.
- Navigation: role-specific bottom navigation on mobile and a compact sidebar on larger screens.
- Components: health summary cards, trend charts, timeline entries, risk panels and action-oriented alerts.
- Accessibility: strong contrast, visible focus states, keyboard support, clear labels and touch-friendly controls.

---

## 7. Scope Definition

### 7.1 Minimum Viable Product

The MVP will include:

- Secure authentication and role-based authorization.
- Patient registration and profile management.
- Consent and privacy acknowledgement.
- Clinical encounter management.
- Recording of symptoms and selected vital signs.
- Relevant medical and family history.
- Health timeline and trend visualization.
- Clinician-configurable threshold alerts.
- One AI-assisted disease-risk assessment.
- Prediction explanation and clinical review.
- Appointment and follow-up management.
- Audit logging and basic operational reports.
- Installable PWA shell, application manifest and service worker.
- Offline fallback and controlled synchronization of permitted draft data.

### 7.2 Post-MVP Features

- SMS, email or push reminders, subject to authorization and infrastructure.
- Integration with supported medical devices.
- Additional disease models after separate validation.
- Pharmacy, inventory or billing integration.
- Referral-management workflow.
- Broader institutional analytics.
- Standards-based integration with other health systems.

### 7.3 Explicitly Out of Scope for the First Release

- Autonomous diagnosis or treatment recommendation.
- Replacement of clinical personnel.
- Unreviewed emergency decision-making by AI.
- Training a single vague model to diagnose many unrelated diseases.
- Storing complete clinical records indefinitely on a user's device.
- Full hospital billing, pharmacy and laboratory information systems.

---

## 8. Users and Core Capabilities

### Patient

- Register or receive an account through an approved process.
- Review and update permitted profile information.
- Manage consent preferences where applicable.
- Enter symptoms and supported health measurements.
- View health trends, appointments, alerts and clinician guidance.
- Complete an authorized AI risk-assessment questionnaire.
- Review appropriately worded risk results and seek clinical care.

### Nurse or Triage Officer

- Find registered patients.
- Open and manage a triage encounter.
- Record and verify symptoms and vital signs.
- Review alerts and assign priority according to approved rules.
- Route the patient to a clinician.

### Clinician or Doctor

- Review the longitudinal patient record.
- Add consultation notes, findings and follow-up instructions.
- Review AI results, explanations and model limitations.
- Confirm, reject or annotate the relevance of an AI result.
- Resolve alerts and document clinical actions.

### Administrator

- Manage accounts, roles and account status.
- Maintain authorized clinical thresholds and reference settings.
- Review access and activity audit logs.
- Manage application and model versions.
- Generate approved operational reports.

### Authorized Researcher

- Access only approved, de-identified datasets.
- Review aggregate model-performance information.
- Reproduce documented experiments without access to unnecessary identifiers.

---

## 9. PWA Requirements

The PWA is a core product requirement, not an optional enhancement.

### 9.1 Installability

- Provide a valid web application manifest.
- Include original icons in required sizes.
- Define application name, short name, start URL, theme color and background color.
- Run over HTTPS outside local development.
- Support installation on compatible Android, desktop and other modern browsers.

### 9.2 Service Worker Strategy

- Cache the application shell and versioned static assets.
- Provide a branded offline fallback page.
- Use network-first handling for current clinical information.
- Use cache-first handling only for safe, versioned static resources.
- Never cache sensitive API responses indiscriminately.
- Clear or rotate caches during logout and application upgrades where appropriate.

### 9.3 Offline Operation

- Permit approved forms to be saved as encrypted or otherwise protected local drafts when practical.
- Mark offline information clearly as unsynchronized.
- Synchronize queued drafts only after authentication and connectivity checks.
- Detect conflicts instead of silently overwriting newer server records.
- Prevent offline AI results when the required model, data freshness or safety conditions cannot be guaranteed.
- Prevent critical clinical actions from appearing complete before server confirmation.

### 9.4 PWA User Experience

- Display connection and synchronization status.
- Provide retry and recovery actions after failed synchronization.
- Notify users when an updated application version is ready.
- Preserve safe form progress during short connection interruptions.
- Support responsive layouts from small phones to desktop screens.
- Meet appropriate accessibility expectations, targeting WCAG 2.2 AA principles.

### 9.5 PWA Acceptance Criteria

- The application can be installed from a supported browser.
- The application launches in standalone display mode after installation.
- The shell and offline fallback load without a network connection.
- Sensitive server responses are not exposed through unsafe caching.
- Draft synchronization can recover from a connection failure without duplication.
- Core pages remain usable on common mobile viewport sizes.
- An automated PWA audit reports no unresolved critical issues.

---

## 10. Functional Workflows

### Health Monitoring

```text
Patient or clinician records a measurement
                    ↓
System validates value, unit, source and time
                    ↓
Measurement is stored or safely queued as a draft
                    ↓
Approved threshold rules are evaluated on the server
                    ↓
Normal result → update timeline and chart
Abnormal result → create alert → authorized clinician reviews
```

### AI-Assisted Risk Assessment

```text
Consent, eligibility and required inputs confirmed
                    ↓
Inputs are validated and transformed consistently
                    ↓
Versioned AI model estimates risk probability
                    ↓
Risk band, explanation and limitations are recorded
                    ↓
Authorized clinician reviews the result
                    ↓
Clinical feedback and any follow-up action are documented
```

### Offline Draft Synchronization

```text
Connection is unavailable
        ↓
Permitted form saved locally as an unsynchronized draft
        ↓
Connection and authenticated session return
        ↓
Server validates draft and checks for conflicts
        ↓
Successful sync → server ID recorded → local draft removed
Conflict/error → retain draft → show clear resolution action
```

---

## 11. Proposed Technical Architecture

### Client Layer

- Laravel Blade views with responsive Bootstrap components.
- Modular JavaScript for interactivity and synchronization.
- Chart.js for longitudinal health visualizations.
- Web app manifest and service worker for PWA capabilities.
- IndexedDB only for strictly controlled local drafts and metadata.

### Application Layer

- Laravel application for authentication, authorization, validation, clinical workflows, reporting and auditing.
- REST-style internal API for PWA actions.
- Server-side alert evaluation.
- Queue support for non-critical background notifications when infrastructure permits.

### Data Layer

- MySQL as the authoritative clinical data store.
- Laravel migrations and seeders for reproducible setup.
- Encrypted secrets and protected backups.
- De-identified research views or exports.

### AI Layer

- Python and FastAPI inference service.
- Scikit-learn pipeline containing preprocessing and the selected model.
- Strict, versioned request and response schemas.
- Model artifact checksum, model card and evaluation report.
- No unnecessary patient identifiers in inference requests.

### High-Level Structure

```text
Installed PWA / Web Browser
           │
           │ HTTPS
           ▼
Laravel Application and Internal API
      │                         │
      ▼                         ▼
MySQL Database          Python AI Service
                                │
                                ▼
                      Versioned Model Pipeline
```

---

## 12. Initial Data Model

The first database design should include the following entities:

| Entity | Purpose |
|---|---|
| `users` | Authentication identity and account state |
| `roles` and permissions | Authorized system capabilities |
| `patients` | Patient identity and demographic profile |
| `staff_profiles` | Medical-centre personnel details |
| `consents` | Consent type, version, decision and timestamp |
| `medical_histories` | Relevant personal and family history |
| `encounters` | Triage, consultation and follow-up visits |
| `vital_signs` | Timestamped measurements, units and verification source |
| `symptom_entries` | Structured symptoms and duration |
| `laboratory_results` | Relevant investigation results and units |
| `appointments` | Scheduled consultations and follow-ups |
| `alerts` | Threshold events, priority, ownership and resolution |
| `predictions` | Inputs reference, output, model version and timestamp |
| `prediction_explanations` | Important contributing factors and display content |
| `clinical_reviews` | Human assessment of a prediction or alert |
| `model_versions` | Model provenance, status, metrics and checksum |
| `notifications` | In-app and approved external messages |
| `audit_logs` | Security-sensitive access and change history |
| `system_settings` | Versioned operational configuration |

Database records should use appropriate constraints, indexes, timestamps and soft-deletion or archival rules where justified. Age should be calculated from date of birth at the relevant encounter or prediction time rather than stored as a permanently changing value.

---

## 13. AI Research and Integration Strategy

### 13.1 Define the Prediction Problem

Before model development, the project team must document:

- The single target disease or clinical outcome.
- The intended patient population.
- Inclusion and exclusion criteria.
- The prediction time and, where relevant, prediction horizon.
- The reference standard used to establish the correct outcome.
- The variables available before the prediction is made.
- The exact clinical purpose of the result.

### 13.2 Data Preparation

- Obtain institutional and ethical authorization before using real records.
- Build a formal data dictionary with definitions, units and allowed values.
- Remove direct identifiers from the model-development dataset.
- Assess missingness, duplicate records, implausible values and inconsistent units.
- Document all exclusions and transformations.
- Examine outcome prevalence and class imbalance.

### 13.3 Model Development

- Establish a transparent baseline such as logistic regression.
- Compare only justified alternatives such as decision tree, random forest or gradient boosting.
- Keep all preprocessing inside a reproducible pipeline.
- Split data by patient so the same person cannot leak across development and evaluation sets.
- Prefer a time-based or appropriately independent evaluation set when the data permits.
- Tune models using development data only.

### 13.4 Evaluation

Report more than overall accuracy. Evaluation should include:

- Sensitivity and specificity.
- Precision, recall and F1 score.
- Area under the receiver operating characteristic curve.
- Confusion matrix.
- Calibration and clinically meaningful probability thresholds.
- Performance across relevant demographic groups where sample size permits.
- Missing-input and out-of-distribution behavior.
- Error analysis, limitations and confidence intervals where appropriate.

Thresholds and acceptable performance must be agreed with qualified supervisors or clinicians; they should not be invented solely to make the model appear successful.

### 13.5 Responsible Integration

- Present probability and a clearly defined risk band.
- Explain the most important contributing inputs in understandable language.
- Show the model's intended use and limitations.
- Allow the model to abstain when required information is missing or unsuitable.
- Require clinical review for consequential use.
- Record model version, inputs, output, explanation and reviewer response.
- Provide a controlled mechanism to disable or roll back a model.
- Never describe the result as a confirmed diagnosis.

### 13.6 Research Reporting

The prediction study should be documented using the current TRIPOD+AI reporting framework. A model card should record:

- Intended and prohibited uses.
- Training and evaluation data provenance.
- Input features and preprocessing.
- Algorithm and selected threshold.
- Performance and subgroup findings.
- Known limitations and risks.
- Version, approval state and responsible owner.

---

## 14. Privacy, Security and Clinical Safety

### Privacy and Governance

- Apply the Nigeria Data Protection Act 2023 and applicable institutional requirements.
- Record consent and the lawful purpose for processing where required.
- Collect only data necessary for the defined workflow.
- Establish retention, correction, export and secure disposal procedures.
- Separate identifying clinical records from de-identified AI research data.
- Complete an appropriate privacy and risk assessment before a live pilot.

### Application Security

- Use strong password hashing and secure reset procedures.
- Apply least-privilege role-based authorization.
- Consider multi-factor authentication for privileged users.
- Enforce HTTPS in staging and production.
- Protect against CSRF, injection, broken access control and unsafe file upload.
- Validate every server request regardless of client-side checks.
- Use secure cookie, session timeout and logout behavior.
- Rate-limit authentication and sensitive endpoints.
- Keep secrets outside source control.
- Encrypt protected backups and test restoration.

### Auditability

- Record authentication and authorization-sensitive events.
- Record access to and changes in sensitive clinical records where required.
- Preserve who performed an action, what changed and when it occurred.
- Prevent ordinary users from altering audit history.
- Avoid storing passwords, tokens or excessive medical details in logs.

### Clinical Safety Controls

- Use clinician-approved alert thresholds.
- Keep urgent red-flag rules separate from experimental AI output.
- Display clear emergency guidance where applicable.
- Prevent stale offline information from appearing current.
- Require server confirmation for critical actions.
- Provide model deactivation and rollback procedures.
- Maintain human responsibility for diagnosis and treatment.

---

## 15. Testing and Quality Strategy

### Automated Testing

- Unit tests for validation, risk bands, thresholds and permission policies.
- Feature tests for patient, encounter, alert and prediction workflows.
- API contract tests between Laravel and the AI service.
- Model pipeline tests for schema, preprocessing and deterministic behavior.
- Authorization tests for every protected resource.
- Synchronization tests for retries, conflicts and duplicate prevention.

### Manual and Specialist Testing

- Clinician review of terminology, measurements and workflow.
- Patient-facing usability testing on representative mobile devices.
- Accessibility testing with keyboard and assistive-technology checks.
- Security review based on common web application risks.
- Backup and disaster-recovery exercise.
- Low-bandwidth, intermittent-network and offline testing.

### PWA Testing

- Manifest and service-worker validation.
- Install and update testing.
- Cache inspection for sensitive-data exposure.
- Offline fallback and draft-recovery testing.
- Responsive testing across target screen sizes.
- Automated browser audit plus real-device verification.

### AI Testing

- Data-leakage checks.
- Independent evaluation-set testing.
- Calibration and threshold review.
- Subgroup and error analysis.
- Missing, malformed and extreme-input testing.
- Model-version and rollback testing.

---

## 16. Phased Delivery Roadmap

### Phase 1 — Discovery and Scope (Weeks 1–2)

**Activities**

- [ ] Interview Medical Centre stakeholders.
- [ ] Map current registration, triage, consultation and follow-up workflows.
- [ ] Confirm user roles and responsibilities.
- [ ] Select one disease or outcome for the first AI model.
- [ ] Identify the available dataset and its authorization status.
- [ ] Define functional and non-functional requirements.

**Deliverables**

- [ ] Approved project scope.
- [ ] Stakeholder and user-role register.
- [ ] Current-state and proposed workflow diagrams.
- [ ] Initial software requirements specification.
- [ ] Defined AI prediction question.

**Exit criteria**

- [ ] The prediction target, intended use, users and MVP boundary are unambiguous.

### Phase 2 — Ethics, Privacy and Data Planning (Weeks 2–3)

**Activities**

- [ ] Prepare ethical and institutional approval materials where required.
- [ ] Define consent and privacy workflows.
- [ ] Create the AI data dictionary.
- [ ] Establish de-identification, retention and access procedures.
- [ ] Complete an initial safety and privacy risk assessment.

**Deliverables**

- [ ] Data-management plan.
- [ ] Consent and privacy requirements.
- [ ] Data dictionary.
- [ ] Approval and authorization record.
- [ ] Initial risk register.

**Exit criteria**

- [ ] No real patient data is used without the required authorization and safeguards.

### Phase 3 — UX, PWA Design and Architecture (Weeks 3–4)

**Activities**

- [ ] Create user journeys and mobile-first wireframes.
- [~] Define the original visual design system. — Teal clinical palette, typography, cards, app bar, bottom dock, focus states and responsive spacing are implemented; formal component documentation remains pending.
- [~] Design online, offline, empty, loading and error states. — Offline, empty, connection, delayed page-loading and disabled-feature states are implemented; synchronization-specific and remaining workflow error states are pending.
- [ ] Produce system architecture and entity-relationship diagrams.
- [ ] Define caching, draft synchronization and conflict rules.
- [ ] Specify the Laravel-to-AI API contract.

**Deliverables**

- [ ] Reviewed wireframes or prototype.
- [ ] Design tokens and reusable component inventory.
- [ ] ER diagram and architecture document.
- [ ] PWA caching and synchronization specification.
- [ ] API specification.

**Exit criteria**

- [ ] Stakeholders can validate the intended workflow before implementation begins.

### Phase 4 — Engineering Foundation (Weeks 4–6)

**Activities**

- [x] Initialize Git and the Laravel application. — Laravel 12.70.2 application initialized on the `main` branch.
- [~] Configure environment management and MySQL. — Local environment and SQLite are ready; dedicated MySQL configuration remains pending.
- [x] Create migrations, factories and safe seed data. — Account, profile, consent, medical-history and encounter migrations/factories are ready; environment-gated fictional accounts and clinical data seed without duplication.
- [~] Implement authentication, roles and authorization policies. — Authentication, four role types, active-account enforcement, profile, consent, medical-history, encounter, observation and clinical-result policies are ready; future timeline, alert and appointment resources will require their own policies.
- [~] Establish application layout, CI checks and test framework. — Original responsive layouts, Turbo Drive navigation and PHPUnit are ready; continuous-integration configuration remains pending.
- [x] Add manifest, icons, base service worker and offline fallback. — Manifest, four original PNG icons, service worker and branded offline page verified.

**Deliverables**

- [x] Reproducible local setup. — Installation and XAMPP guidance documented in `README.md`.
- [x] Secure authentication and role foundation. — Registration is patient-only; inactive accounts cannot authenticate.
- [x] Initial database schema. — Authentication, sessions, jobs, cache, account authorization, patient/staff profiles, consent history, medical history, encounters, symptoms, vital-sign observations and clinical results migrated successfully.
- [~] Installable PWA shell. — Technical installability requirements are implemented; representative-device installation verification remains pending.
- [x] Automated baseline test suite. — 80 tests and 406 assertions pass; PHP and JavaScript dependency audits report no known vulnerabilities.

**Exit criteria**

- [~] Authorized users can log in and install a safe application shell. — Login is verified; browser/device installation verification remains pending.

### Phase 5 — Clinical Monitoring MVP (Weeks 6–9)

**Activities**

- [~] Implement patient and staff profiles. — Patient/staff records, typed identifiers, generated patient numbers, patient self-service editing and ownership policies are ready; staff administration UI remains pending.
- [x] Implement consent, medical history and encounters. — Versioned append-only consent, patient-managed structured history and role-protected encounter lifecycle are implemented and tested; institutional approval of final consent wording remains tracked in Phase 2.
- [x] Implement symptoms, vital signs and relevant results. — Append-only structured symptoms, verified vital-sign snapshots and numeric/qualitative clinical results are implemented with units, timestamps, provenance and safe validation; flags are reproduced from source reports rather than inferred.
- [x] Build the longitudinal timeline and trend charts. — Encounters, symptoms, verified vital signs and clinical results are combined chronologically; responsive Chart.js trends are restricted to verified measurements and loaded on demand.
- [ ] Add clinician-approved threshold alerts and resolution workflow.
- [ ] Implement appointments and follow-up actions.
- [ ] Add audit events to sensitive workflows.

**Deliverables**

- [ ] End-to-end patient monitoring workflow.
- [~] Mobile-responsive dashboards. — Patient/staff home experiences, encounter workspace and longitudinal timeline are responsive; alert and appointment summaries remain pending.
- [~] Health charts and alerts. — Responsive verified vital-sign trend charts are complete; clinician-approved threshold alerts remain pending.
- [ ] Appointment and clinical-review functions.
- [~] Permission and workflow tests. — Profile, consent, medical-history, encounter, observation, result and timeline ownership, validation and least-privilege rules are covered; alert and appointment workflows remain pending.

**Exit criteria**

- [ ] A patient journey can be demonstrated without the AI component.

### Phase 6 — Offline-Aware PWA Completion (Weeks 8–10)

**Activities**

- [x] Implement safe static caching and update handling. — Only safe static assets are runtime-cached; navigation uses the network with an offline fallback.
- [ ] Implement approved offline drafts using IndexedDB.
- [ ] Build synchronization, retry and conflict handling.
- [~] Add connection and synchronization indicators. — Online/offline connection status and accessible page-navigation feedback are implemented; synchronization status awaits the draft queue.
- [~] Verify logout and cache-clearing behavior. — Private pages are not cached; explicit draft/cache cleanup awaits offline-draft implementation.
- [ ] Test installation and operation on representative devices.

**Deliverables**

- [ ] Installable, updateable and offline-aware PWA.
- [ ] Synchronization test report.
- [ ] PWA audit report.
- [ ] Device-compatibility record.

**Exit criteria**

- [ ] The application handles connection loss without exposing sensitive cached data or duplicating records.

### Phase 7 — AI Research Pipeline (Weeks 7–11)

**Activities**

- [ ] Acquire only approved data.
- [ ] Conduct data-quality and exploratory analysis.
- [ ] Build leakage-resistant development and evaluation splits.
- [ ] Train baseline and justified comparison models.
- [ ] Evaluate discrimination, calibration, errors and subgroup performance.
- [ ] Select and document the safest suitable model.

**Deliverables**

- [ ] Reproducible training pipeline.
- [ ] Evaluation report and visualizations.
- [ ] Selected versioned model artifact.
- [ ] Model card and limitations statement.
- [ ] TRIPOD+AI-aligned research record.

**Exit criteria**

- [ ] The selected model has documented evidence, limitations and an approved intended use.

### Phase 8 — AI Service and Product Integration (Weeks 11–13)

**Activities**

- [ ] Implement the validated FastAPI inference service.
- [ ] Validate request schemas and required inputs.
- [ ] Integrate secure Laravel service calls.
- [ ] Build risk, explanation and limitation interfaces.
- [ ] Add clinician review and feedback.
- [ ] Implement model registry, audit details, timeout handling and rollback.

**Deliverables**

- [ ] Working end-to-end prediction workflow.
- [ ] Explainable risk presentation.
- [ ] Prediction history and clinical-review trail.
- [ ] AI integration and contract tests.

**Exit criteria**

- [ ] Every prediction is reproducible, versioned, safely worded and reviewable.

### Phase 9 — Verification and Hardening (Weeks 13–16)

**Activities**

- [ ] Complete automated and manual testing.
- [ ] Test authorization across all roles.
- [ ] Review security, privacy and audit behavior.
- [ ] Test load, failure recovery and backup restoration.
- [ ] Conduct accessibility and PWA audits.
- [ ] Resolve high-priority defects.

**Deliverables**

- [ ] Test plan and results.
- [ ] Security and privacy review.
- [ ] Accessibility and performance reports.
- [ ] Backup and restoration record.
- [ ] Release candidate.

**Exit criteria**

- [ ] No unresolved critical security, privacy, clinical-safety or data-integrity defects remain.

### Phase 10 — Pilot Evaluation (Weeks 16–18)

**Activities**

- [ ] Conduct a controlled demonstration or approved pilot.
- [ ] Collect structured feedback from representative users.
- [ ] Measure usability, task completion and system response.
- [ ] Review AI presentation with clinicians.
- [ ] Correct prioritized usability and workflow issues.

**Deliverables**

- [ ] Pilot protocol and feedback summary.
- [ ] Usability evaluation report.
- [ ] Prioritized corrections.
- [ ] Stakeholder acceptance record for the evaluated scope.

**Exit criteria**

- [ ] The system is usable for its declared research purpose and limitations are documented.

### Phase 11 — Deployment and Academic Handover (Weeks 18–20)

**Activities**

- [ ] Prepare a secured staging or approved production-like environment.
- [ ] Configure HTTPS, backups, logs and monitoring.
- [ ] Finalize installation and administrator instructions.
- [ ] Produce user manuals and training materials.
- [ ] Complete dissertation, diagrams, presentation and demonstration script.
- [ ] Tag the evaluated release and archive model artifacts.

**Deliverables**

- [ ] Deployed evaluated release.
- [ ] Installation, administration and user guides.
- [ ] Source-code and model documentation.
- [ ] Final academic report and presentation materials.
- [ ] Maintenance and known-limitations register.

**Exit criteria**

- [ ] The evaluated release can be reproduced, demonstrated, maintained and clearly distinguished from future work.

---

## 17. Milestones

| Milestone | Target | Evidence of completion |
|---|---:|---|
| M1: Scope approved | End of Week 2 | Requirements and AI question signed off |
| M2: Architecture approved | End of Week 4 | Wireframes, ER diagram and PWA plan reviewed |
| M3: Foundation ready | End of Week 6 | Authenticated, installable PWA shell |
| M4: Monitoring MVP ready | End of Week 9 | Complete non-AI patient journey demonstrated |
| M5: AI model selected | End of Week 11 | Evaluation report and model card completed |
| M6: AI integrated | End of Week 13 | Reviewed end-to-end prediction workflow |
| M7: Release candidate | End of Week 16 | Critical verification checks passed |
| M8: Pilot completed | End of Week 18 | Usability and stakeholder feedback documented |
| M9: Final handover | End of Week 20 | Evaluated release and academic documentation delivered |

---

## 18. Definition of Done

A feature is complete only when:

- Its acceptance criteria are met.
- Server-side validation and authorization are implemented.
- Relevant automated tests pass.
- Mobile, loading, empty, offline and error states are handled where applicable.
- Accessibility requirements have been considered.
- Sensitive actions are audited appropriately.
- Documentation is updated.
- No known critical security or data-integrity issue remains.

The project is complete for its academic scope when:

- The evaluated PWA can be installed and used on target devices.
- Core monitoring workflows operate correctly.
- Offline behavior is controlled and recoverable.
- The AI model has a precise intended use and documented evaluation.
- Predictions are versioned, explained and subject to human review.
- Privacy, security and safety controls are demonstrated.
- Testing, user evaluation and limitations are reported honestly.
- The source code, setup process, model pipeline and final report are reproducible.

---

## 19. Success Measures

Final targets should be agreed during requirements and clinical review. The evaluation should cover:

### Product and PWA Measures

- Successful completion of critical user tasks.
- Installation success on selected target devices.
- Responsive operation on supported mobile viewports.
- Safe recovery from intermittent connectivity.
- Synchronization success without silent duplication or data loss.
- Acceptable accessibility and automated PWA audit results.

### Engineering Measures

- Passing automated test suite.
- No critical broken-access-control findings.
- Successful backup restoration.
- Acceptable response time under the documented test conditions.
- Traceability of sensitive changes through audit records.

### AI Measures

- Sensitivity, specificity, precision, recall, F1 score and AUROC.
- Calibration and threshold justification.
- Error and subgroup analysis.
- Clinician assessment of usefulness and presentation.
- Percentage of predictions with complete model-version and review records.

### Research Measures

- Requirements traced to implementation and tests.
- Transparent methodology and reproducible experiments.
- Honest reporting of limitations and threats to validity.
- Appropriate stakeholder and user evaluation.

---

## 20. Key Risks and Mitigation

| Risk | Impact | Mitigation |
|---|---|---|
| Prediction scope is vague or covers many diseases | Invalid or unsafe model | Select one outcome with an explicit intended use |
| Insufficient or unauthorized clinical data | Delays and privacy risk | Resolve approvals and data suitability before AI development |
| Public dataset does not represent local patients | Misleading local claims | Treat it as a prototype and require appropriate local evaluation |
| Patient records leak through PWA caches | Serious privacy breach | Cache only safe resources and inspect storage behavior explicitly |
| Offline synchronization duplicates or overwrites records | Clinical data-integrity failure | Use idempotency, conflict detection and server confirmation |
| Model appears accurate because of data leakage | Invalid research conclusion | Split by patient and isolate evaluation data |
| Accuracy is reported without calibration or error analysis | Misleading evaluation | Use a complete, clinically relevant metric set |
| Users interpret risk as diagnosis | Potential patient harm | Use careful wording, limitations and clinician review |
| Scope expands into a full hospital system | Schedule failure | Enforce MVP and post-MVP boundaries |
| Weak role permissions expose records | Security and confidentiality breach | Apply least privilege and test every protected resource |
| AI service becomes unavailable | Interrupted workflow | Fail safely, preserve monitoring features and show clear status |
| Alert fatigue reduces usefulness | Important alerts ignored | Use clinician-approved thresholds, priorities and review feedback |

---

## 21. Academic Documentation Plan

The software roadmap should align with the final report:

### Chapter One — Introduction

- Background to the study.
- Statement of the problem.
- Aim and objectives.
- Research questions.
- Significance, scope and limitations.

### Chapter Two — Literature Review

- Mobile health and PWAs.
- Electronic health monitoring.
- Clinical decision-support systems.
- Machine learning for disease-risk prediction.
- Explainability, privacy, bias and responsible health AI.
- Review of related systems and identified research gap.

### Chapter Three — Methodology and System Design

- Research and software-development method.
- Requirements gathering.
- Existing and proposed system analysis.
- Use cases, workflows, ER diagram and architecture.
- PWA caching and synchronization design.
- Dataset, preprocessing, model development and evaluation method.
- Ethical, privacy and security controls.

### Chapter Four — Implementation and Results

- Development environment and system modules.
- PWA implementation.
- Database and API implementation.
- AI experiments and final model results.
- Integration, interfaces and testing results.
- User evaluation.

### Chapter Five — Conclusion and Recommendations

- Summary of findings.
- Achievement of objectives.
- Contributions and limitations.
- Recommendations and future work.

---

## 22. Immediate Action List

The following decisions should be completed before full implementation:

1. [ ] Confirm the official project title.
2. [ ] Confirm or replace the proposed product name, **EdeHealth Sentinel**.
3. [ ] Select the first disease or clinical outcome.
4. [ ] Identify the available dataset and establish whether its use is authorized.
5. [ ] Interview representatives of the Medical Centre.
6. [ ] Confirm the precise patient, nurse, clinician and administrator workflows.
7. [ ] Approve the MVP boundary and post-MVP list.
8. [ ] Create the software requirements specification and initial risk register.
9. [ ] Produce wireframes and the database ER diagram.
10. [~] Initialize the Laravel, MySQL and PWA engineering foundation. — Laravel and PWA foundations are ready; dedicated MySQL setup remains pending.

---

## 23. Reference Frameworks

The project should consult the following authoritative frameworks during design and reporting:

- Nigeria Data Protection Act 2023.
- World Health Organization guidance on ethics and governance of artificial intelligence for health.
- World Health Organization regulatory considerations for artificial intelligence for health.
- TRIPOD+AI guidance for transparent reporting of clinical prediction-model studies.
- OWASP application-security guidance.
- WCAG 2.2 accessibility principles.

---

## 24. Closing Statement

The distinguishing strength of this project should be the responsible combination of an installable, low-bandwidth PWA with longitudinal health monitoring and transparent, clinician-supervised AI. The first release should prioritize a reliable monitoring workflow, one defensible prediction use case and strong privacy safeguards. Additional diseases and integrations should be introduced only after the initial model and system have been evaluated for their intended population and context.
