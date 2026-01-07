<?php
$path = __DIR__ . '/../app/Http/Controllers/SettingsController.php';
$s = file_get_contents($path);
if ($s === false) { echo "ERR_READ\n"; exit(1); }
if (substr($s,0,3) === "\xEF\xBB\xBF") {
    $s = substr($s,3);
    file_put_contents($path, $s);
    echo "BOM_REMOVED\n";
} else {
    echo "NO_BOM\n";
}
