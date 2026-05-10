<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$model = new \App\Models\BarangaySettingsModel();
$existingSettings = $model->first();
$basicData = [
    'barangay_name'  => 'Test',
    'municipality'   => 'Test',
    'province'       => 'Test',
    'contact_number' => '1234',
];
if ($existingSettings) {
    $model->update($existingSettings['id'], $basicData);
} else {
    $model->insert($basicData);
}
echo "Done";
