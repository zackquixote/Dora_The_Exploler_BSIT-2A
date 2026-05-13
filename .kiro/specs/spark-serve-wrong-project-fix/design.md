# Spark Serve Wrong Project Bugfix Design

## Overview

This bugfix addresses a critical development environment issue where the hardcoded baseURL configuration in `app/Config/App.php` causes interference between different CodeIgniter 4 projects when running development servers. The bug manifests when `php spark serve` is executed in other CodeIgniter 4 project directories, causing them to serve or redirect to the "Dora_The_Exploler_BSIT-2A" project instead of the current project. The fix involves implementing dynamic baseURL detection for development environments while preserving existing production behavior.

## Glossary

- **Bug_Condition (C)**: The condition that triggers the bug - when `php spark serve` is executed in a different CodeIgniter 4 project directory but serves content from this project
- **Property (P)**: The desired behavior when running development servers - each project should serve only its own content with proper path resolution
- **Preservation**: Existing production behavior and current project functionality that must remain unchanged by the fix
- **baseURL**: The configuration property in `app/Config/App.php` that defines the root URL for the CodeIgniter application
- **Development Environment**: When `CI_ENVIRONMENT` is set to 'development' and `php spark serve` is being used
- **Project Isolation**: The requirement that each CodeIgniter project should operate independently without interfering with others

## Bug Details

### Bug Condition

The bug manifests when a user runs `php spark serve` in any CodeIgniter 4 project directory other than "Dora_The_Exploler_BSIT-2A". The hardcoded baseURL configuration `'http://localhost/Dora_The_Exploler_BSIT-2A/public/'` in `app/Config/App.php` causes the development server to generate incorrect links, redirects, and asset paths that point to this specific project instead of dynamically detecting the current project path.

**Formal Specification:**
```
FUNCTION isBugCondition(input)
  INPUT: input of type DevelopmentServerContext
  OUTPUT: boolean
  
  RETURN input.command == 'php spark serve'
         AND input.currentProjectDirectory != 'Dora_The_Exploler_BSIT-2A'
         AND input.environment == 'development'
         AND baseURL_is_hardcoded_to_this_project()
END FUNCTION
```

### Examples

- **Example 1**: Running `php spark serve` in `/xampp/htdocs/MyOtherProject/` serves links pointing to `http://localhost/Dora_The_Exploler_BSIT-2A/public/` instead of `http://localhost:8080/`
- **Example 2**: Development server in another project displays "Dora_The_Exploler_BSIT-2A" content or shows broken links due to incorrect path resolution
- **Example 3**: Asset loading (CSS, JS, images) fails in other projects because paths resolve to this project's directory structure
- **Edge Case**: Multiple CodeIgniter projects running simultaneously on different ports all incorrectly reference this project's baseURL

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Production environment behavior must continue to use the configured baseURL exactly as before
- When `php spark serve` is executed within the "Dora_The_Exploler_BSIT-2A" project directory, it must continue to work correctly
- All other CodeIgniter commands and functionality must remain unaffected
- Non-development environments (production, testing) must maintain current baseURL behavior

**Scope:**
All inputs that do NOT involve running development servers in other CodeIgniter projects should be completely unaffected by this fix. This includes:
- Production deployments with configured baseURL
- Testing environments with specific baseURL configurations
- Other CodeIgniter CLI commands (migrations, cache clear, etc.)
- Normal web server operation (Apache/Nginx) with virtual hosts

## Hypothesized Root Cause

Based on the bug description and code analysis, the most likely issues are:

1. **Hardcoded BaseURL Configuration**: The primary cause is the hardcoded baseURL in `app/Config/App.php` set to `'http://localhost/Dora_The_Exploler_BSIT-2A/public/'`
   - This static configuration doesn't adapt to different project contexts
   - CodeIgniter uses this baseURL for generating all internal links and redirects
   - The development server inherits this configuration regardless of the actual project being served

2. **Lack of Environment-Specific BaseURL Detection**: The configuration doesn't differentiate between development and production environments
   - No dynamic detection of the current development server context
   - Missing logic to override baseURL when `php spark serve` is active

3. **Missing Development Server Integration**: CodeIgniter's development server doesn't automatically adjust the baseURL configuration
   - The `spark serve` command doesn't inject dynamic baseURL values
   - No mechanism to detect when running under the built-in development server

4. **Environment Variable Override Not Configured**: The `.env` file has the `app.baseURL` commented out, preventing environment-specific overrides

## Correctness Properties

Property 1: Bug Condition - Development Server Project Isolation

_For any_ development server context where `php spark serve` is executed in a different CodeIgniter 4 project directory, the fixed configuration SHALL dynamically detect the appropriate baseURL for the current project context, ensuring that all generated links, redirects, and asset paths point to the correct project without interference from hardcoded values.

**Validates: Requirements 2.1, 2.2, 2.3**

Property 2: Preservation - Production Environment Behavior

_For any_ environment context that is NOT a development server (production, testing, or normal web server operation), the fixed configuration SHALL produce exactly the same baseURL behavior as the original configuration, preserving all existing functionality for configured baseURL usage in non-development environments.

**Validates: Requirements 3.1, 3.2, 3.3**

## Fix Implementation

### Changes Required

Assuming our root cause analysis is correct:

**File**: `app/Config/App.php`

**Function**: `baseURL` property configuration

**Specific Changes**:
1. **Dynamic BaseURL Detection**: Implement logic to detect when running under `php spark serve`
   - Check for development environment and built-in server context
   - Generate appropriate baseURL based on server host and port
   - Fall back to configured baseURL for non-development contexts

2. **Environment-Aware Configuration**: Modify the baseURL property to be context-sensitive
   - Use environment detection to determine appropriate baseURL source
   - Implement conditional logic for development vs production environments

3. **Development Server Integration**: Add detection for CodeIgniter's built-in development server
   - Check `$_SERVER` variables to identify development server context
   - Generate dynamic baseURL using server host and port information

4. **Preserve Production Behavior**: Ensure existing baseURL configuration remains active for production
   - Maintain backward compatibility with current production deployments
   - Only apply dynamic behavior in development contexts

5. **Environment Variable Support**: Enable `.env` file override capability
   - Uncomment and configure `app.baseURL` in `.env` for environment-specific overrides
   - Provide clear documentation for different environment configurations

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that demonstrate the bug on unfixed code, then verify the fix works correctly and preserves existing behavior.

### Exploratory Bug Condition Checking

**Goal**: Surface counterexamples that demonstrate the bug BEFORE implementing the fix. Confirm or refute the root cause analysis. If we refute, we will need to re-hypothesize.

**Test Plan**: Create test CodeIgniter projects in different directories and run `php spark serve` to observe incorrect baseURL behavior. Document the specific failures and path resolution issues on the UNFIXED code.

**Test Cases**:
1. **Different Project Directory Test**: Run `php spark serve` in `/xampp/htdocs/TestProject/` and verify it serves this project's content (will fail on unfixed code)
2. **Port-Based Development Server Test**: Start development server on different ports and verify baseURL reflects correct port (will fail on unfixed code)
3. **Asset Path Resolution Test**: Check that CSS/JS/image paths resolve correctly in other projects (will fail on unfixed code)
4. **Multiple Concurrent Servers Test**: Run multiple development servers simultaneously and verify isolation (will fail on unfixed code)

**Expected Counterexamples**:
- Development servers in other projects generate links pointing to `http://localhost/Dora_The_Exploler_BSIT-2A/public/`
- Possible causes: hardcoded baseURL, lack of environment detection, missing development server integration

### Fix Checking

**Goal**: Verify that for all inputs where the bug condition holds, the fixed function produces the expected behavior.

**Pseudocode:**
```
FOR ALL input WHERE isBugCondition(input) DO
  result := getBaseURL_fixed(input)
  ASSERT expectedBehavior(result)
END FOR
```

### Preservation Checking

**Goal**: Verify that for all inputs where the bug condition does NOT hold, the fixed function produces the same result as the original function.

**Pseudocode:**
```
FOR ALL input WHERE NOT isBugCondition(input) DO
  ASSERT getBaseURL_original(input) = getBaseURL_fixed(input)
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking because:
- It generates many test cases automatically across the input domain
- It catches edge cases that manual unit tests might miss
- It provides strong guarantees that behavior is unchanged for all non-buggy inputs

**Test Plan**: Observe behavior on UNFIXED code first for production environments and normal web server operation, then write property-based tests capturing that behavior.

**Test Cases**:
1. **Production Environment Preservation**: Observe that production deployments use configured baseURL correctly on unfixed code, then write test to verify this continues after fix
2. **Web Server Virtual Host Preservation**: Observe that Apache/Nginx virtual hosts work correctly on unfixed code, then write test to verify this continues after fix
3. **CLI Command Preservation**: Observe that other CodeIgniter CLI commands work correctly on unfixed code, then write test to verify this continues after fix

### Unit Tests

- Test baseURL generation for different development server contexts
- Test environment detection logic (development vs production)
- Test that production environments continue to use configured baseURL
- Test edge cases (missing server variables, invalid configurations)

### Property-Based Tests

- Generate random development server configurations and verify correct baseURL generation
- Generate random production environment contexts and verify preservation of configured baseURL behavior
- Test that all non-development server contexts continue to work across many scenarios

### Integration Tests

- Test full development workflow with `php spark serve` in different project directories
- Test switching between development and production environments
- Test that multiple CodeIgniter projects can run development servers simultaneously without interference
- Test that production deployments remain unaffected by development server changes