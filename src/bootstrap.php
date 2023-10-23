<?php
/**
 * The bootstrap file creates and returns the container.
 */
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\MyPDO;
use Dotenv\Dotenv;
use DI\ContainerBuilder;

$dotnet = Dotenv::createImmutable(__DIR__ . '/../');
$dotnet->load();

$containerBuilder = new ContainerBuilder;
$containerBuilder->useAttributes(true);
$containerBuilder->addDefinitions([
    'MyPDO' => function () {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $dbName = $_ENV['DB_NAME'];
        $dsn = "mysql:host={$host};dbname={$dbName}";
        return new MyPDO($dsn, $username, $password);
    },
]);
$container = $containerBuilder->build();
return $container;