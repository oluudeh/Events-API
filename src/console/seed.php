<?php

$container = require_once __DIR__ . '/../bootstrap.php';

$db = $container->get('MyPDO');

use Database\Seeders\EventsSeeder;

EventsSeeder::run($db);
