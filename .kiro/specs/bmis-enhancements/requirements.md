# Requirements Document

## Introduction

This document defines requirements for seven enhancements to the Barangay Management Information System (BMIS), a CodeIgniter 4 / MariaDB web application used by barangay staff to manage residents, households, blotter cases, certificates, and officials. The enhancements improve case tracking transparency, data integrity, operational efficiency, and administrative visibility through new workflows, automated rules, and dashboard analytics.

---

## Glossary

- **BMIS**: Barangay Management Information System — the CodeIgniter 4 web application being enhanced.
- **Blotter_Controller**: The `App\Controllers\Blotter` class that handles all blotter case operations.
- **Resident_Controller**: The `App\Controllers\Resident` class that handles all resident operations.
- **Dashboard_Controller**: The `App\Controllers\Admin\Dashboard` class that renders the admin dashboard.
- **BlotterRecord**: A row in the `blotter_records` table representing a filed barangay blotter case.
- **BlotterTimeline**: A row in the `blotter_timeline` table recording a status change event for a BlotterRecord.
- **BlotterParty**: A row in the `blotter_parties` table linking a resident or outsider to a BlotterRecord with a role (complainant, respondent, witness).
- **Resident**: A row in the `residents` table representing a registered barangay resident.
- **Household**: A row in the `households` table representing a family unit with a head resident.
- **TransferHistory**: A row in the `resident_transfer_history` table (new) recording a household change for a Resident.
- **Open_Case**: A BlotterRecord whose `status` is one of: Pending, Investigating, Ongoing, For Hearing.
- **Resolved_Case**: A BlotterRecord whose `status` is Settled or Dismissed.
- **Overdue_Case**: An Open_Case whose `created_at` date is 30 or more calendar days before the current date.
- **Resolution_Rate**: The percentage of Resolved_Cases out of all BlotterRecords filed within a given calendar month.
- **Staff_User**: An authenticated user with role `staff` or `admin` who operates the BMIS.
- **KPI_Card**: A summary metric card displayed on the admin dashboard.
- **Population_Growth_Chart**: A line chart on the admin dashboard showing monthly resident counts.

---

## Requirements

### Requirement 1: Case Status Timeline with Mandatory Remarks

**User Story:** As a Staff_User, I want every status change on a blotter case to require a remarks field, so that there is always a documented reason for each transition in the case history.

#### Acceptance Criteria

1. IF a Staff_User submits a status change for a BlotterRecord where the submitted status differs from the current status AND the `action_taken` field is empty or contains only whitespace, THEN the Blotter_Controller SHALL reject the submission and return a validation error without persisting any changes.
2. WHEN a Staff_User submits a status change where the submitted status differs from the current status and the `action_taken` field contains at least one non-whitespace character (maximum 1000 characters), THE Blotter_Controller SHALL insert a BlotterTimeline row containing the old status, new status, `action_taken` text as remarks, the user ID of the Staff_User, and the current timestamp.
3. IF a Staff_User submits a status update where the submitted status value is identical to the current BlotterRecord status, THEN the Blotter_Controller SHALL NOT insert a BlotterTimeline row.
4. WHEN a Staff_User views a BlotterRecord detail page, THE BMIS SHALL display each BlotterTimeline entry in ascending chronological order (oldest first) showing: old status (or "Initial" if null), new status, remarks text, and the date/time of the change.
5. WHEN a BlotterRecord is first created, THE Blotter_Controller SHALL insert an initial BlotterTimeline row with `old_status` as NULL, `new_status` as "Pending", and remarks as "Case filed".
6. IF a Staff_User submits a status change without providing `action_taken` text, THEN THE BMIS SHALL display an inline validation error message adjacent to the `action_taken` field without reloading the page.

---

### Requirement 2: Duplicate Case Detection

**User Story:** As a Staff_User, I want the system to warn me before saving a new blotter case when the same complainant and respondent already have an open case, so that I can avoid filing redundant cases.

#### Acceptance Criteria

1. WHEN a Staff_User submits a new blotter case where both the complainant and respondent are registered residents (each has a non-null `resident_id`), THE Blotter_Controller SHALL query `blotter_parties` joined with `blotter_records` to check whether any Open_Case (status in: Pending, Investigating, Ongoing, For Hearing) already has a complainant party with the same `resident_id` AND a respondent party with the same `resident_id` as the submitted case, treating A-vs-B and B-vs-A as the same pair.
2. WHEN exactly one duplicate Open_Case is detected, THE Blotter_Controller SHALL return a JSON warning response containing the existing case number and the URL to the existing case detail page, without saving the new BlotterRecord.
3. WHEN more than one duplicate Open_Case is detected, THE Blotter_Controller SHALL return a JSON warning response listing all matching case numbers and their detail page URLs, without saving the new BlotterRecord.
4. WHEN a Staff_User confirms submission after receiving a duplicate warning, THE Blotter_Controller SHALL save the new BlotterRecord and its parties normally via a second request that includes a `force_save=1` flag.
5. WHEN a Staff_User cancels after a duplicate warning, THE BMIS SHALL keep the user on the create form with all previously entered field values preserved (no page reload).
6. IF the duplicate check AJAX request fails due to a network or server error, THEN THE BMIS SHALL display an error message and allow the Staff_User to proceed with saving the case.
7. THE Blotter_Controller SHALL perform duplicate detection only when both the complainant and respondent are registered residents; cases where either party is an outsider (no `resident_id`) SHALL NOT trigger duplicate detection.

---

### Requirement 3: Case Resolution Timer

**User Story:** As a Staff_User, I want to see how many days each open blotter case has been active, and a clear visual flag for overdue cases, so that I can prioritize cases that need urgent attention.

#### Acceptance Criteria

1. WHEN a Staff_User views the blotter index page, THE BMIS SHALL display the number of calendar days elapsed since `created_at` for each BlotterRecord whose status is an Open_Case status (Pending, Investigating, Ongoing, For Hearing), shown adjacent to the Status column.
2. WHEN a BlotterRecord qualifies as an Overdue_Case (days elapsed ≥ 30), THE BMIS SHALL display an "Overdue" badge alongside the days-elapsed count on the blotter index page row for that record.
3. WHEN a Staff_User views a BlotterRecord detail page for an Open_Case, THE BMIS SHALL display the days-elapsed count and, if the case is an Overdue_Case, the "Overdue" badge in the header bar that contains the case number and incident date.
4. THE BMIS SHALL display the days-elapsed value as an integer number of calendar days; a case created today SHALL display 0 days.
5. WHEN a BlotterRecord status is Settled, Dismissed, Referred, or Unsettled, THE BMIS SHALL NOT display a days-elapsed count or Overdue badge for that record on either the index or detail page.
6. THE BMIS SHALL define the overdue threshold as 30 calendar days; a case with a days-elapsed value of exactly 30 or greater SHALL be treated as an Overdue_Case.

---

### Requirement 4: Resident Transfer History

**User Story:** As a Staff_User, I want the system to automatically log when a resident moves from one household to another, so that I can view a complete household transfer history for any resident.

#### Acceptance Criteria

1. WHEN a Staff_User saves a Resident update that changes the `household_id` to a different non-null value, THE Resident_Controller SHALL insert a TransferHistory row recording: `resident_id`, `from_household_id` (previous value), `to_household_id` (new value), and `transfer_date` (current date).
2. WHEN a Staff_User saves a Resident update that changes the `household_id` from a non-null value to null, THE Resident_Controller SHALL insert a TransferHistory row with `from_household_id` set to the previous value, `to_household_id` as NULL, and `transfer_date` as the current date.
3. IF a Staff_User saves a Resident update where the `household_id` value is unchanged (including null-to-null), THEN the Resident_Controller SHALL NOT insert a TransferHistory row.
4. WHEN a Staff_User views a Resident detail page, THE BMIS SHALL display a "Transfer History" tab (the 6th tab, after Cases) showing all TransferHistory rows for that Resident in reverse chronological order (newest first), including: from household number (or "None"), to household number (or "None"), and transfer date.
5. WHEN a Resident has no TransferHistory rows, THE BMIS SHALL display a "No transfer history recorded" message in the Transfer History tab.
6. THE BMIS SHALL maintain a `resident_transfer_history` table with columns: `id` (PK auto-increment), `resident_id` (FK to residents.id ON DELETE CASCADE), `from_household_id` (FK to households.id ON DELETE SET NULL, nullable), `to_household_id` (FK to households.id ON DELETE SET NULL, nullable), `transfer_date` (date NOT NULL), `created_at` (datetime DEFAULT current_timestamp).

---

### Requirement 5: Deceased/Transferred Auto-Cleanup

**User Story:** As a Staff_User, I want the system to automatically remove a resident from their household when their status is changed to "deceased" or "transferred", so that household membership records stay accurate without requiring manual cleanup.

#### Acceptance Criteria

1. WHEN a Staff_User changes a Resident's `status` to "deceased" or "transferred" AND that Resident's `household_id` is not NULL, THE Resident_Controller SHALL set that Resident's `household_id` to NULL, `left_household_date` to the current date, and `is_household_head` to 0 in the same database transaction.
2. WHEN a Resident whose `is_household_head` is 1 has their status changed to "deceased" or "transferred", THE Blotter_Controller SHALL return a JSON response with a `requires_head_replacement: true` flag and the list of remaining active household members (id and full name) so the client can display a replacement prompt.
3. WHEN a Staff_User designates a replacement household head in response to the prompt, THE Resident_Controller SHALL update the `head_resident_id` on the Household to the replacement resident's ID and set `is_household_head` to 1 on the replacement Resident in the same transaction as the status update.
4. WHEN a Staff_User dismisses the household head prompt without selecting a replacement, THE Resident_Controller SHALL complete the status update and household removal and set `head_resident_id` to NULL on the affected Household.
5. THE Resident_Controller SHALL apply the auto-cleanup logic for both the AJAX `updateStatus` endpoint and the standard form-based `update` method.
6. WHEN the auto-cleanup is applied, THE Resident_Controller SHALL insert a TransferHistory row with `resident_id` set to the affected resident, `from_household_id` set to the resident's previous `household_id`, `to_household_id` as NULL, and `transfer_date` as the current date.

---

### Requirement 6: Case Resolution Rate KPI

**User Story:** As an Admin, I want to see a KPI card on the dashboard showing the percentage of cases resolved this month compared to last month, so that I can quickly assess the barangay's case resolution performance.

#### Acceptance Criteria

1. THE Dashboard_Controller SHALL compute the current month's Resolution_Rate as: `(count of BlotterRecords with status IN ('Settled','Dismissed') AND updated_at within the current calendar month / count of all BlotterRecords with created_at within the current calendar month) * 100`, rounded to one decimal place.
2. THE Dashboard_Controller SHALL compute the previous month's Resolution_Rate using the same formula applied to the prior calendar month's date range for both the numerator and denominator.
3. WHEN the current month's Resolution_Rate is strictly greater than the previous month's Resolution_Rate, THE BMIS SHALL display an upward green trend indicator and the signed percentage-point difference (e.g., "+5.2pp") alongside the KPI_Card value.
4. WHEN the current month's Resolution_Rate is strictly less than the previous month's Resolution_Rate, THE BMIS SHALL display a downward red trend indicator and the signed percentage-point difference (e.g., "−3.1pp") alongside the KPI_Card value.
5. WHEN the current month's Resolution_Rate equals the previous month's Resolution_Rate, THE BMIS SHALL display a neutral dash indicator (no percentage-point label) alongside the KPI_Card value.
6. WHEN there is no data for the previous calendar month (zero BlotterRecords created in that month), THE BMIS SHALL display a neutral dash indicator alongside the KPI_Card value.
7. WHEN there are zero BlotterRecords filed in the current calendar month, THE BMIS SHALL display "N/A" as the Resolution_Rate value on the KPI_Card.
8. THE BMIS SHALL display the Resolution_Rate KPI_Card in the admin dashboard's primary stat cards row (the `ds-grid-4` row) alongside the existing Total Residents, Households, Certificates, and Blotter Cases cards.

---

### Requirement 7: Population Growth Chart

**User Story:** As an Admin, I want to see a line chart on the dashboard showing the total resident count per month for the past 12 months broken down by purok/sitio, so that I can monitor population trends across the barangay.

#### Acceptance Criteria

1. THE Dashboard_Controller SHALL query the `residents` table to compute, for each of the 12 calendar months ending with the current month, the count of residents where `deleted_at IS NULL` AND `created_at` is on or before the last day of that month.
2. THE Dashboard_Controller SHALL compute a separate monthly count series for each of the 5 sitio values (Purok Malipayon, Purok Masagana, Purok Cory, Purok Kawayan, Purok Pagla-um) plus one series for residents with a NULL or empty `sitio` labeled "Unassigned".
3. WHEN the Dashboard_Controller renders the admin dashboard, THE BMIS SHALL pass to the view: a `popGrowthLabels` array of 12 month-year strings (e.g., ["Jun 2025", …, "May 2026"]) and a `popGrowthDatasets` array of objects each with `name` (sitio label) and `data` (array of 12 integer counts).
4. WHEN the admin dashboard page renders, THE BMIS SHALL initialize a Chart.js multi-line chart using `popGrowthLabels` and `popGrowthDatasets`, with one line per sitio series, displayed in a dedicated card in the Analytics section after the Civil Status chart.
5. WHEN a sitio series has a value of 0 for all 12 months, THE BMIS SHALL exclude that series from `popGrowthDatasets` so it does not appear in the chart.
6. THE Population_Growth_Chart SHALL display month-year labels on the X-axis, resident count on the Y-axis with a minimum of 0, and a legend identifying each line by sitio name.
7. WHEN all sitio series are zero across all 12 months (no residents registered), THE BMIS SHALL display a "No population data available" message in place of the chart canvas.
