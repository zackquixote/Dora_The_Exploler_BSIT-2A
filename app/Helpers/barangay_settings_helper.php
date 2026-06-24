<?php

use App\Models\BarangaySettingsModel;

if (!function_exists('barangay_settings')) {
    /**
     * Get the single-row barangay settings with safe defaults.
     * Cached per-request (static) to avoid repeated DB hits across partial views.
     */
    function barangay_settings(): array
    {
        static $cached = null;
        if (is_array($cached)) {
            return $cached;
        }

        $model = new BarangaySettingsModel();
        $settings = $model->first();

        if (!$settings) {
            $settings = [];
        }

        $cached = array_merge([
            'barangay_name' => 'Barangay',
            'municipality'  => 'Municipality',
            'province'      => 'Province',
            'contact_number'=> '',
            'logo'          => '',
            'photo'         => '',
            'logo_size'     => 56,
        ], $settings);

        // sanity bounds for logo_size
        $size = (int) ($cached['logo_size'] ?? 56);
        if ($size < 32 || $size > 160) {
            $size = 56;
        }
        $cached['logo_size'] = $size;

        return $cached;
    }
}

