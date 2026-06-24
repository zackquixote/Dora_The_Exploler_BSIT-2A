# UI Enhancement Plan — Show “Barangay Photo” on Login (Banner)

## Goal
When an admin uploads **Barangay Photo** in **Admin → Settings**, that photo should also show on:
- **Admin Login**
- **Resident Login**

As a **background banner** (header/banner style), while keeping the existing **logo** shown as the circular image.

## Current Behavior (as of now)
- Uploaded **photo** is saved to: `public/assets/img/` and stored in DB field: `barangay_settings.photo`
- The **sidebar** uses: `photo` → fallback to `logo` → fallback to `tabu.jpg`
- The **login pages** use only: `logo` (no photo on login)

## Proposed UI/UX (Banner)
### Layout concept
On both login pages:
- Keep the card + form as-is.
- Add a **banner layer** (image + overlay) that gives a modern “hero header” look.
- Keep the **logo circle** in the header so branding remains consistent.

### Fallback rules
- If `barangay_settings.photo` exists: use it as banner image
- Else: show the normal gradient background only (no banner image)

### Accessibility & readability
- Always apply a subtle **dark overlay** on the banner image so text remains readable.
- Ensure responsive behavior on mobile (banner should not make the card too tall).

## Implementation Approach (no code applied yet)

### 1) Update shared login stylesheet
File:
- `public/assets/css/login-ui.css`

Add a reusable banner style, for example:
- `.login-card.has-banner { position: relative; }`
- `.login-card.has-banner::before { background-image: var(--login-banner-url); ... }`
- `.login-card.has-banner::after { overlay gradient ... }`

Where the banner image is injected via a CSS variable:
- `--login-banner-url: url('...');`

### 2) Update Admin Login view
File:
- `app/Views/login.php`

Steps:
- Read `$photoFile = $settings['photo'] ?? ''`
- If it exists, set the banner URL in a safe way and add a class:
  - `class="login-card has-banner"`
  - `style="--login-banner-url:url('...')"`

### 3) Update Resident Login view
File:
- `app/Views/portal/login.php`

Steps:
- This view already uses `barangay_settings()` helper.
- Read `$photoFile = $bs['photo'] ?? ''`
- Apply the same `has-banner` + `--login-banner-url` behavior.

## Acceptance Criteria
- Uploading **Barangay Photo** in Settings updates DB and the image appears as a banner on:
  - `/login` (Admin login)
  - `/portal/login` (Resident login)
- If no photo exists, both login pages look normal (no broken UI).
- Banner is responsive and text is readable on mobile/desktop.

## Risks / Notes
- Banner URLs must be handled carefully to avoid broken CSS if filenames contain unexpected characters (we already generate safe filenames).
- Very large images may load slowly; we may optionally add:
  - `loading="lazy"` is not applicable for CSS backgrounds, but we can keep the image area small and rely on browser caching.

## Next Step (after you approve)
I will implement the banner in:
- `public/assets/css/login-ui.css`
- `app/Views/login.php`
- `app/Views/portal/login.php`

