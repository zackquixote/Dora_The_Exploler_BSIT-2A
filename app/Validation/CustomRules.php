<?php

namespace App\Validation;

/**
 * CustomRules – project-specific validation rules.
 *
 * Register in app/Config/Validation.php under $ruleSets.
 */
class CustomRules
{
    /**
     * Ensure a date is not in the future and not before 1900-01-01.
     *
     * Usage: 'birthdate' => 'required|valid_date|valid_birthdate'
     */
    public function valid_birthdate(string $value, string $params = '', array $data = [], ?string &$error = null): bool
    {
        $ts = strtotime($value);

        if ($ts === false) {
            $error = 'Invalid date format.';
            return false;
        }

        if ($ts > time()) {
            $error = 'Birthdate cannot be in the future.';
            return false;
        }

        if ($ts < strtotime('1900-01-01')) {
            $error = 'Birthdate must be after January 1, 1900.';
            return false;
        }

        return true;
    }

    /**
     * Ensure a date is not in the future (generic — for incident dates, etc.).
     *
     * Usage: 'incident_date' => 'required|valid_date|not_future_date'
     */
    public function not_future_date(string $value, string $params = '', array $data = [], ?string &$error = null): bool
    {
        $ts = strtotime($value);

        if ($ts === false) {
            $error = 'Invalid date format.';
            return false;
        }

        // Allow today but not tomorrow onwards
        if ($ts > strtotime('today 23:59:59')) {
            $error = 'Date cannot be in the future.';
            return false;
        }

        return true;
    }

    /**
     * Ensure a text field is not purely numeric (names, occupations, etc.).
     *
     * Usage: 'occupation' => 'permit_empty|not_numeric_only'
     */
    public function not_numeric_only(string $value, string $params = '', array $data = [], ?string &$error = null): bool
    {
        if (trim($value) !== '' && ctype_digit(str_replace([' ', '-', '.'], '', $value))) {
            $error = 'This field cannot contain numbers only.';
            return false;
        }
        return true;
    }

    /**
     * Ensure a purpose/text field is not blank whitespace.
     *
     * Usage: 'purpose' => 'required|not_blank'
     */
    public function not_blank(string $value, string $params = '', array $data = [], ?string &$error = null): bool
    {
        if (trim($value) === '') {
            $error = 'This field cannot be blank or contain only spaces.';
            return false;
        }
        return true;
    }
}
