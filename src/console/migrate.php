<?php
$container = require_once __DIR__ . '/../bootstrap.php';

$db = $container->get('MyPDO');

$files = [
    'create_city_table.sql',
    'create_country_table.sql',
    'create_event_table.sql',
];

$dir = __DIR__ . '/../database/migrations';

foreach ($files as $file) {
    $sql = file_get_contents($dir . '/' . $file);
    $db->exec($sql);
}
