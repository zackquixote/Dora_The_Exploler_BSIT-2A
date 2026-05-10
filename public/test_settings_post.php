<?php
// Simulate posting to /admin/settings/update
$url = 'http://localhost:8080/admin/settings/update';
$data = [
    'barangay_name' => 'Test',
    'municipality' => 'Test',
    'province' => 'Test',
    'contact_number' => '1234',
    'pos_captain' => '1',
    'pos_secretary' => '2',
    'pos_treasurer' => '3',
    'pos_sk' => '4'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// We might need a session or bypass auth, but let's see if we hit auth redirect
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpcode\n";
echo substr(strip_tags($response), 0, 500); // Strip HTML to just see the error text
?>
