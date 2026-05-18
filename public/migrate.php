<?php
chdir(__DIR__ . '/../');
echo "Running php spark migrate...<br><br>";
exec('php spark migrate 2>&1', $output, $return_var);
foreach ($output as $line) {
    echo htmlspecialchars($line) . "<br>";
}
echo "<br>Return code: " . $return_var;

