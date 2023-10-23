<?php

use App\Helpers\MyPDO;
use App\Repositories\EventRepository;
use App\Services\EventService;
use Database\Seeders\EventsSeeder;
use PHPUnit\Framework\TestCase;


class EventServiceTest extends TestCase
{
    private static ?MyPDO $myPDO = null;
    private static ?EventService $eventService = null;


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

        $repository = new EventRepository(self::$myPDO);
        self::$eventService = new EventService($repository);
    }

    public static function tearDownAfterClass(): void
    {
        self::$myPDO = null;
    }


    public function testFetchEventsWithNoParams(): void
    {
        $result = self::$eventService->fetchEvents();
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('totalPages', $result);
        $this->assertGreaterThan(0, count($result['data']));
    }

    public function testFetchEventsWithTerm(): void
    {
        $term = 'Zimbabwe';
        $result = self::$eventService->fetchEvents(term: $term);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('totalPages', $result);
        $this->assertGreaterThan(0, count($result['data']));
    }

    public function testFetchEventsWithDate(): void
    {
        $date = '2024-04-03';
        $result = self::$eventService->fetchEvents(date: $date);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('totalPages', $result);
        $this->assertGreaterThan(0, count($result['data']));
    }

    public function testFetchEventsWithTermAndDate(): void
    {
        $term = 'Zimbabwe';
        $date = '2024-04-03';
        $result = self::$eventService->fetchEvents(term: $term, date: $date);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('totalPages', $result);
        $this->assertGreaterThan(0, count($result['data']));
    }
}
