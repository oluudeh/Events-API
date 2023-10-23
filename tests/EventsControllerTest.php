<?php

use App\Controllers\EventsController;
use App\EventApp;
use App\Helpers\MyPDO;
use Database\Seeders\EventsSeeder;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;


class EventsControllerTest extends TestCase
{

    private static ?MyPDO $myPDO = null;


    public static function setUpBeforeClass(): void
    {
        self::$myPDO = new MyPDO('sqlite::memory:', '', '');

        $files = [
            'create_city_table.sql',
            'create_country_table.sql',
            'create_event_table.sql',
        ];
        
        $dir = __DIR__ . '/sql';
        
        foreach ($files as $file) {
            $sql = file_get_contents($dir . '/' . $file);
            self::$myPDO->exec($sql);
        }
        EventsSeeder::run(self::$myPDO);
    }

    public static function tearDownAfterClass(): void
    {
        self::$myPDO = null;
    }

    private function runApp(): EventApp
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->useAttributes(true);
        $containerBuilder->addDefinitions([
            'MyPDO' => self::$myPDO,
        ]);
        $container = $containerBuilder->build();

        return new EventApp(
            routes: [
                '/' => [
                    'GET' => [EventsController::class, 'index']
                ]
            ],
            logger: $this->createMock(LoggerInterface::class),
            container: $container,
        );
    }

    public function testSuccessNoPayload(): void
    {
        $app = $this->runApp();
        $response = $app->handle(Request::create('/', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
        $resArr = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $resArr);
        $this->assertArrayHasKey('page', $resArr);
        $this->assertArrayHasKey('total', $resArr);
        $this->assertArrayHasKey('size', $resArr);
        $this->assertArrayHasKey('totalPages', $resArr);
        $this->assertGreaterThan(0, count($resArr['data']));
    }
    public function testSuccessWithTerm(): void
    {
        $app = $this->runApp();
        $term = 'Zimbabwe';
        $response = $app->handle(Request::create("/?term={$term}", 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
        $resArr = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $resArr);
        $this->assertArrayHasKey('page', $resArr);
        $this->assertArrayHasKey('total', $resArr);
        $this->assertArrayHasKey('size', $resArr);
        $this->assertArrayHasKey('totalPages', $resArr);
        $this->assertGreaterThan(0, count($resArr['data']));
        $this->assertStringContainsString($term, $response->getContent());
    }

    public function testSuccessWithDate(): void
    {
        $app = $this->runApp();
        $date = '2024-04-03';
        $response = $app->handle(Request::create("/?date={$date}", 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
        $resArr = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $resArr);
        $this->assertArrayHasKey('page', $resArr);
        $this->assertArrayHasKey('total', $resArr);
        $this->assertArrayHasKey('size', $resArr);
        $this->assertArrayHasKey('totalPages', $resArr);
        $this->assertGreaterThan(0, count($resArr['data']));
        $event = $resArr['data'][0];
        $this->assertLessThanOrEqual($date, $event['startDate']);
        $this->assertGreaterThanOrEqual($date, $event['endDate']);

    }
    public function testSuccessWithTermAndDate(): void
    {
        $app = $this->runApp();
        $term = 'Zimbabwe';
        $date = '2024-04-04';
        $response = $app->handle(Request::create("/?term={$term}&date={$date}", 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
        $resArr = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $resArr);
        $this->assertArrayHasKey('page', $resArr);
        $this->assertArrayHasKey('total', $resArr);
        $this->assertArrayHasKey('size', $resArr);
        $this->assertArrayHasKey('totalPages', $resArr);
        $this->assertGreaterThan(0, count($resArr['data']));
        $this->assertStringContainsString($term, $response->getContent());
        $event = $resArr['data'][0];
        $this->assertLessThanOrEqual($date, $event['startDate']);
        $this->assertGreaterThanOrEqual($date, $event['endDate']);
    }

    public function testWrongDate(): void
    {
        $app = $this->runApp();
        $response = $app->handle(Request::create('/?date=2024-02-30', 'GET'));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('errors', json_decode($response->getContent(), true));
    }

    public function testPastDate(): void
    {
        $app = $this->runApp();
        $response = $app->handle(Request::create('/?date=2023-02-03', 'GET'));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('errors', json_decode($response->getContent(), true));
    }

}
