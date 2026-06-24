# Resident Verification Expansion Plan

## Goal

Expand the resident portal registration flow from a simple self-service account request into a staged identity verification workflow that:

- collects stronger identity evidence during registration
- lets admins review uploaded ID documents before activation
- optionally confirms the resident's contact channel with OTP
- keeps unverified residents blocked from protected portal features
- records review actions for audit, privacy, and accountability

This plan is designed around the current codebase, which already has:

- `ResidentPortalAuth::register()` and `ResidentPortalAuth::login()`
- `resident_accounts` with basic `pending/active/rejected/suspended` flow
- admin review screen in `Admin\PortalAccounts`
- notification infrastructure in `NotificationService`
- resident portal routes under `portal/*`

## Current State

### Existing registration flow

- Public registration form: `app/Views/portal/register.php`
- Controller: `app/Controllers/ResidentPortalAuth.php`
- It currently asks for:
  - first name
  - last name
  - birthdate
  - email
  - phone
  - password
- It tries to match an existing row in `residents`.
- If matched, it inserts a `resident_accounts` row with status `pending`.
- Admin approval currently happens in `app/Controllers/Admin/PortalAccounts.php`.

### Existing limitations

- No national ID upload
- No ID number capture
- No admin document review workflow
- No request-for-resubmission flow
- No OTP verification stage
- No secure handling rules for uploaded identity documents
- No proper state machine beyond basic pending/active/rejected/suspended

## Expanded Product Idea

### Proposed verification journey

1. Resident starts portal registration.
2. Resident provides identity and contact details.
3. Resident uploads one or more national ID images.
4. System creates a registration/application in a pending verification state.
5. Admin reviews the submitted ID and identity fields.
6. Admin either:
   - approves identity
   - rejects the application
   - requests corrected ID images or additional proof
7. Optional OTP stage starts after ID approval.
8. Resident enters OTP sent to email or phone.
9. Only then is the resident portal account marked usable.

### What this improves

- reduces fake or duplicate portal signups
- creates a reviewable approval queue for admins
- prevents immediate activation without proof of identity
- creates a privacy-aware flow for storing and removing sensitive ID documents
- supports future compliance improvements

## Recommended Architecture

## Core Design Choice

Use `resident_accounts` as the main portal account record, but add a dedicated verification layer rather than overloading everything into one or two status values.

Recommended structure:

- `resident_accounts`
  - remains the login/account table
  - stores activation status and contact confirmation status
- new `resident_verifications`
  - stores application-stage identity verification data
  - stores ID metadata, admin review state, and review decisions
- optional `resident_verification_files`
  - stores one-to-many uploaded files
  - supports front/back ID images and future extra documents

This separation is better than putting every document/review field inside `resident_accounts`, because:

- login concerns stay separate from verification concerns
- multiple uploads and re-submissions are easier to support
- audit/review history is cleaner
- future document types can be added without bloating the account table

## Data Model Proposal

### 1. Keep `resident_accounts`, but evolve status handling

Current values:

- `pending`
- `active`
- `disabled`
- plus controller logic already uses `rejected` and `suspended`

Recommended normalized account statuses:

- `pending_verification`
- `pending_otp`
- `active`
- `rejected`
- `suspended`

Alternative:

- keep account `status` more coarse:
  - `pending`
  - `active`
  - `rejected`
  - `suspended`
- and use `resident_verifications.status` for the detailed workflow

Preferred approach for this repo:

- keep `resident_accounts.status` for login gating
- add detailed workflow state to `resident_verifications.status`

That gives simpler login checks and more flexible verification workflow.

### 2. New table: `resident_verifications`

Purpose:

- represents the resident’s ID verification application

Suggested fields:

- `id`
- `resident_account_id`
- `resident_id` nullable at first if matching happens later
- `full_name_submitted`
- `address_submitted`
- `contact_email_submitted`
- `contact_phone_submitted`
- `national_id_number`
- `status`
- `review_notes`
- `rejection_reason`
- `requested_resubmission_reason`
- `otp_required` boolean
- `otp_channel` enum `sms/email`
- `otp_verified_at`
- `submitted_at`
- `reviewed_at`
- `reviewed_by`
- `created_at`
- `updated_at`

Recommended verification statuses:

- `pending_id_upload`
- `pending_admin_review`
- `needs_resubmission`
- `pending_otp`
- `verified`
- `rejected`

### 3. New table: `resident_verification_files`

Purpose:

- stores uploaded document files separately from the verification record

Suggested fields:

- `id`
- `resident_verification_id`
- `file_type`
- `storage_path`
- `original_name`
- `mime_type`
- `file_size`
- `is_primary`
- `uploaded_at`
- `created_at`

Suggested `file_type` values:

- `national_id_front`
- `national_id_back`
- `selfie_with_id`
- `supporting_document`

### 4. Optional OTP fields

If OTP stays simple, store OTP state in `resident_verifications`.

If you want retries/history/rate limiting to be robust, create:

- `resident_verification_otps`

Suggested fields:

- `id`
- `resident_verification_id`
- `channel`
- `destination_masked`
- `otp_hash`
- `expires_at`
- `attempt_count`
- `max_attempts`
- `sent_at`
- `verified_at`
- `invalidated_at`

Preferred approach:

- phase 1: store basic OTP fields on `resident_verifications`
- phase 2: extract into a dedicated OTP table if retries/history become important

## Workflow Design

### State machine

Recommended high-level transitions:

1. Registration form opened
2. Account shell created
3. Verification record created as `pending_id_upload`
4. Once required ID files are uploaded: `pending_admin_review`
5. Admin approves:
   - if OTP disabled -> `verified` and account becomes `active`
   - if OTP enabled -> `pending_otp` and account becomes `pending_otp`
6. Resident submits correct OTP:
   - verification becomes `verified`
   - account becomes `active`
7. Admin rejects:
   - verification becomes `rejected`
   - account becomes `rejected`
8. Admin requests better proof:
   - verification becomes `needs_resubmission`
   - resident sees resubmission reason and uploads again

### Login gating

Update `ResidentPortalAuth::login()` to handle:

- `pending_verification`
  - show “Your ID is under review.”
- `pending_otp`
  - redirect to OTP verify screen instead of full portal access
- `rejected`
  - show reason and next action
- `needs_resubmission`
  - show “Please upload a clearer/new ID image”

### Matching to resident records

You have two valid options:

#### Option A: Match to existing `residents` during registration

Resident supplies:

- full name
- address
- contact
- national ID number
- ID image

System still tries to match an existing barangay resident row.

Pros:

- easier if portal should only be for already-registered residents
- keeps current logic closer to existing code

Cons:

- harder for admins to handle cases where resident data does not perfectly match existing records

#### Option B: Let admin confirm resident linkage during review

Create verification/application first.
Admin review screen can:

- link the application to an existing resident
- reject if not a valid barangay resident
- request correction if details mismatch

Pros:

- more flexible and realistic for real-world data mismatches
- fewer false rejections at self-service stage

Cons:

- admin review UI is more complex

Recommended for this repo:

- use a hybrid model
- attempt auto-match during registration
- if exact match fails, still allow submission but flag it for manual review

That gives a better user experience and avoids blocking real residents because of formatting differences.

## UI / UX Plan

### Resident side

#### Registration form

Expand `app/Views/portal/register.php` from a simple account form into a multi-section verification form.

Recommended sections:

1. Identity details
   - full name
   - birthdate
   - address
   - national ID number
2. Contact details
   - phone
   - email
   - preferred OTP channel
3. Credentials
   - password
   - confirm password
4. ID uploads
   - national ID front
   - national ID back
   - optional supporting document
5. Consent / privacy notice
   - explain why the ID is collected
   - explain who can see it
   - explain retention/deletion policy

#### Resident status pages

Add a verification status page in the portal auth flow:

- `portal/verification-status`

Possible messages:

- ID received
- under admin review
- needs resubmission
- rejected with reason
- waiting for OTP
- verified and active

#### OTP screen

Add:

- `portal/verify-otp`

Features:

- enter OTP
- resend OTP with cooldown
- show masked destination

### Admin side

#### Verification queue dashboard

Extend or replace `Admin/portal_accounts.php` with a proper verification queue.

Recommended tabs:

- awaiting review
- needs resubmission
- pending OTP
- approved
- rejected

Each queue row should show:

- applicant name
- submitted address
- email / phone
- linked resident record if matched
- submitted date
- current status
- preview thumbnail of uploaded ID

#### Verification detail page

Add a full-screen review page for one application.

Show:

- submitted identity info
- resident record match suggestion
- uploaded files with enlarge/download controls
- admin notes panel
- audit trail for that verification

Admin actions:

- approve ID
- reject with reason
- request new ID images
- request additional supporting document
- select OTP required yes/no

## Security and Privacy Plan

### File storage

Uploaded national ID images are sensitive.

Do not place them in a public URL path that anyone can browse directly.

Recommended:

- store outside the web root if possible
- or store in a protected area and serve through a controller that checks admin authorization

In this repo, the practical safe pattern is:

- store files under a non-public writable/protected folder
- create an admin-only download/view endpoint

Avoid:

- direct public exposure under `public/uploads/...`

### Access control

Only `admin` role should view ID images by default.

Optional:

- allow specific `staff` roles only if truly needed

### Retention policy

Recommended policy options:

- keep files while application is pending
- after verification:
  - archive securely for a limited retention period
  - or delete images and keep only metadata + audit record

Preferred policy:

- keep verified documents only as long as operationally necessary
- define retention clearly in the privacy note

### Audit logging

Log every review action:

- reviewer user ID
- action type
- target verification/account ID
- old status
- new status
- reason / note
- timestamp

Important repo note:

- this codebase currently has mixed logging approaches
- `IdCardService::logAction()` writes to `audit_logs`
- many controllers use `LogModel::addLog()`
- there is also evidence that legacy `audit_logs` usage changed over time

Recommended approach:

- use one consistent service or helper for verification review logging
- if `tbl_logs` is the true current audit trail, prefer that
- if you need structured review history, add a dedicated `resident_verification_reviews` or write structured JSON metadata

## Notifications Plan

### Resident notifications

Use existing `NotificationService` where possible.

Send notifications for:

- registration received
- ID review approved
- ID rejected
- resubmission requested
- OTP sent
- OTP verified

Preferred channels:

- `in_app`
- `email`
- `sms`

Use the resident’s chosen OTP channel for OTP delivery.

### Admin notifications

Notify admins when:

- a new verification application is submitted
- a resubmission arrives

This can initially be:

- dashboard counter
- in-app notification

Later:

- email to configured admin recipients

## Recommended Implementation Phases

### Phase 1: Data and workflow foundation

Deliverables:

- migration for `resident_verifications`
- migration for `resident_verification_files`
- status normalization in `resident_accounts`
- model classes for new tables
- service for verification workflow and status transitions

Acceptance criteria:

- verification data can be stored separately from the account
- status transitions are centralized in one service

### Phase 2: Resident registration + ID upload

Deliverables:

- expanded registration form
- upload validation
- secure file storage
- verification status page
- initial resident-facing notifications

Acceptance criteria:

- resident can submit full identity verification package
- files are stored securely and not publicly exposed

### Phase 3: Admin review workflow

Deliverables:

- admin verification queue
- verification detail page
- approve / reject / request resubmission actions
- audit trail for admin decisions

Acceptance criteria:

- admin can review documents and move applications through the workflow
- review actions are logged

### Phase 4: OTP confirmation

Deliverables:

- OTP generation and send flow
- resident OTP verify screen
- resend and expiry handling
- lockout / retry rules

Acceptance criteria:

- approved residents cannot activate until OTP is verified when OTP is required

### Phase 5: Hardening and cleanup

Deliverables:

- retention/deletion job for old ID files
- dashboard metrics for pending verification counts
- better duplicate detection / resident matching
- tests for full verification flow

Acceptance criteria:

- privacy, operational visibility, and reliability are improved

## Routes To Add

### Public / resident auth routes

Recommended additions:

- `portal/verification-status`
- `portal/verification/resubmit`
- `portal/verify-otp`
- `portal/resend-otp`

### Admin routes

Recommended additions under admin group:

- `portal-verifications`
- `portal-verifications/view/{id}`
- `portal-verifications/approve/{id}`
- `portal-verifications/reject/{id}`
- `portal-verifications/request-resubmission/{id}`
- `portal-verifications/file/{fileId}`

## Services To Add

Recommended new services:

- `ResidentVerificationService`
  - create application
  - store uploads
  - transition statuses
  - coordinate account status updates
- `ResidentVerificationOtpService`
  - generate/send OTP
  - verify OTP
  - handle expiry and resend cooldown
- `VerificationDocumentService`
  - secure storage
  - protected file retrieval
  - cleanup/retention

## Models To Add

Recommended models:

- `ResidentVerificationModel`
- `ResidentVerificationFileModel`
- optionally `ResidentVerificationOtpModel`

## Controller Changes

### `ResidentPortalAuth`

Expand:

- `register()`
  - validate full verification submission
  - create account + verification record
  - store files
  - show pending status page
- `login()`
  - handle new pending states
  - redirect pending OTP users correctly
- add:
  - `verificationStatus()`
  - `verifyOtp()`
  - `resendOtp()`
  - `resubmitVerification()`

### `Admin\PortalAccounts`

Recommended path:

- either evolve this controller into a verification controller
- or create a dedicated `Admin\PortalVerifications`

Preferred:

- create `Admin\PortalVerifications`
- keep `PortalAccounts` focused on active/suspended account management

That gives cleaner boundaries:

- `PortalVerifications`: approval workflow
- `PortalAccounts`: ongoing account admin

## Validation Rules

### Resident-facing validation

- required:
  - full name
  - address
  - at least one contact channel
  - national ID number
  - national ID front image
- file checks:
  - mime type whitelist
  - max file size
  - image dimensions sanity check
- password checks:
  - minimum length
  - confirm password match

### Admin-facing validation

- reject reason required on rejection
- resubmission reason required when requesting new docs
- OTP channel required if OTP is enabled

## Edge Cases To Plan For

- resident already has active account
- resident has rejected verification and wants to retry
- resident submits blurry image
- resident uploads unsupported file type
- resident uses someone else’s ID
- contact info changes during pending review
- OTP expires
- OTP resend abused
- admin approves but notification sending fails
- resident record matching is ambiguous

## Testing Strategy

### Unit tests

Add tests for:

- status transition rules
- OTP generation/expiry
- file metadata persistence
- account activation gating

### Integration / feature tests

Add tests for:

- register with ID upload
- submit verification and land in pending state
- admin approve flow
- admin reject flow
- admin request resubmission flow
- OTP verification flow
- blocked access before activation

### Security tests

Add tests for:

- unauthorized user cannot access ID image endpoint
- resident cannot access protected portal pages until active
- invalid/expired OTP is rejected

## Trade-Offs and Recommendations

### Manual review vs automation

Your idea correctly prioritizes manual admin review first.

Recommendation:

- do not start with OCR or automatic ID verification
- manual review is simpler, safer, and more realistic for a barangay workflow

### OTP optional vs mandatory

Recommendation:

- make OTP configurable in settings
- start with:
  - OTP optional in local/dev
  - OTP enabled in production once SMS/email channel is reliable

### Single-step vs multi-step registration form

Recommendation:

- use a staged form UI if possible
- but submit to the server as one validated workflow

This keeps UX understandable without making backend state management too fragmented.

## Suggested Delivery Order

1. database design and migrations
2. resident verification models/services
3. registration form + secure file upload
4. admin review queue + detail page
5. account gating updates
6. OTP flow
7. audit and notification hardening
8. retention cleanup and metrics

## Practical Next Step

Recommended next implementation step:

Design and implement the schema first.

Specifically:

1. add `resident_verifications`
2. add `resident_verification_files`
3. decide final status enums
4. create `ResidentVerificationService`

Once that foundation exists, the rest of the UI and workflow becomes much easier to build cleanly.

## Proposed Scope For The First Build

If you want a realistic first milestone, I recommend this exact scope:

- resident registration form with ID upload
- secure storage for ID images
- admin review queue
- approve / reject / request resubmission
- account remains blocked until approval
- email OTP optional but not required in milestone 1

Then milestone 2 adds:

- OTP verification
- more granular dashboard metrics
- document retention cleanup

## Summary

Your idea is strong and fits this project well.

The best way to expand it is:

- separate verification data from the core login account
- introduce a proper status machine
- build a dedicated admin verification queue
- keep uploaded ID documents protected
- activate the portal account only after review, and optionally after OTP

The codebase already has enough portal auth, admin review, routing, and notification structure to support this with a clean phased implementation.
