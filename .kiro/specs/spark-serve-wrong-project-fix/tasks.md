# Implementation Plan

- [ ] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - Development Server Project Isolation Bug
  - **CRITICAL**: This test MUST FAIL on unfixed code - failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior - it will validate the fix when it passes after implementation
  - **GOAL**: Surface counterexamples that demonstrate the bug exists
  - **Scoped PBT Approach**: For deterministic bugs, scope the property to the concrete failing case(s) to ensure reproducibility
  - Test that when `php spark serve` is executed in a different CodeIgniter 4 project directory, the system serves only the current project without interference from hardcoded baseURL configuration
  - Create a test CodeIgniter project in a different directory (e.g., `/xampp/htdocs/TestProject/`)
  - Run `php spark serve` in the test project directory
  - Verify that generated links, redirects, and asset paths point to the correct project context (not hardcoded to "Dora_The_Exploler_BSIT-2A")
  - Test the bug condition: `input.command == 'php spark serve' AND input.currentProjectDirectory != 'Dora_The_Exploler_BSIT-2A' AND input.environment == 'development' AND baseURL_is_hardcoded_to_this_project()`
  - The test assertions should match the Expected Behavior Properties from design: dynamic baseURL detection for development environments
  - Run test on UNFIXED code
  - **EXPECTED OUTCOME**: Test FAILS (this is correct - it proves the bug exists)
  - Document counterexamples found to understand root cause (e.g., "Development server in TestProject generates links pointing to http://localhost/Dora_The_Exploler_BSIT-2A/public/ instead of http://localhost:8080/")
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Production Environment and Current Project Behavior
  - **IMPORTANT**: Follow observation-first methodology
  - Observe behavior on UNFIXED code for non-buggy inputs (production environments, current project development server)
  - Test Case 1: Observe that when `php spark serve` is executed within the "Dora_The_Exploler_BSIT-2A" project directory, it serves correctly on unfixed code
  - Test Case 2: Observe that production/non-development environments use the configured baseURL correctly on unfixed code
  - Test Case 3: Observe that other CodeIgniter commands function normally on unfixed code
  - Write property-based tests capturing observed behavior patterns from Preservation Requirements
  - Property-based testing generates many test cases for stronger guarantees
  - Test the non-bug condition: `NOT (input.command == 'php spark serve' AND input.currentProjectDirectory != 'Dora_The_Exploler_BSIT-2A')`
  - Run tests on UNFIXED code
  - **EXPECTED OUTCOME**: Tests PASS (this confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.3_

- [ ] 3. Fix for CodeIgniter spark serve project path issue

  - [ ] 3.1 Implement dynamic baseURL detection for development environments
    - Modify `app/Config/App.php` to implement environment-aware baseURL configuration
    - Add logic to detect when running under `php spark serve` (check `$_SERVER` variables for built-in server context)
    - Implement dynamic baseURL generation using server host and port for development environments
    - Add conditional logic: use dynamic baseURL for development servers, fall back to configured baseURL for production
    - Enable environment variable support by ensuring `.env` file can override baseURL when needed
    - _Bug_Condition: isBugCondition(input) where input.command == 'php spark serve' AND input.currentProjectDirectory != 'Dora_The_Exploler_BSIT-2A' AND input.environment == 'development' AND baseURL_is_hardcoded_to_this_project()_
    - _Expected_Behavior: expectedBehavior(result) - dynamic baseURL detection for development environments while preserving production behavior_
    - _Preservation: Production environment behavior and current project functionality must remain unchanged_
    - _Requirements: 1.1, 1.2, 1.3, 2.1, 2.2, 2.3, 3.1, 3.2, 3.3_

  - [ ] 3.2 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - Development Server Project Isolation Fixed
    - **IMPORTANT**: Re-run the SAME test from task 1 - do NOT write a new test
    - The test from task 1 encodes the expected behavior
    - When this test passes, it confirms the expected behavior is satisfied
    - Run bug condition exploration test from step 1
    - **EXPECTED OUTCOME**: Test PASSES (confirms bug is fixed)
    - Verify that development servers in different projects now generate correct baseURL without interference
    - _Requirements: Expected Behavior Properties from design - dynamic baseURL detection_

  - [ ] 3.3 Verify preservation tests still pass
    - **Property 2: Preservation** - Production Environment and Current Project Behavior Preserved
    - **IMPORTANT**: Re-run the SAME tests from task 2 - do NOT write new tests
    - Run preservation property tests from step 2
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions)
    - Confirm all tests still pass after fix (no regressions)
    - Verify production environments continue to use configured baseURL
    - Verify current project development server continues to work correctly
    - Verify other CodeIgniter commands remain unaffected

- [ ] 4. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
  - Verify that the fix successfully resolves the project isolation issue
  - Confirm that production behavior is preserved
  - Validate that multiple CodeIgniter projects can now run development servers independently