<?php

use App\Helpers\MyPDO;
use App\Repositories\EventRepository;
use Database\Seeders\EventsSeeder;
use PHPUnit\Framework\TestCase;

class EventRepositoryTest extends TestCase
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

    public function testPaginate(): void
    {
        $repository = new EventRepository(self::$myPDO);
        
        $term = 'Zimbabwe';
        $date = '2024-04-03';

        $columns = [
            'event.id',
            'event.name',
            'event.start_date',
            'event.end_date',
            'city.id',
            'city.name',
            'country.id',
            'country.name'
        ];

        $joins = [
            ['type' => 'INNER', 'table' => 'city', 'condition' => 'event.city_id = city.id'],
            ['type' => 'INNER', 'table' => 'country', 'condition' => 'city.country_id = country.id'],
        ];

        $conditions = [
            [
                'group' => [
                    ['column' => 'city.name', 'operator' => 'LIKE', 'value' => "%{$term}%"],
                    ['column' => 'country.name', 'operator' => 'LIKE', 'value' => "%{$term}%"],
                ],
                'glue' => 'OR'
            ],
            ['column' => 'event.start_date', 'operator' => '<=', 'value' => $date],
            ['column' => 'event.end_date', 'operator' => '>=', 'value' => $date]
        ];

        $results = $repository->paginate(
            columns: $columns,
            joins: $joins,
            conditions: $conditions,
        );

        $this->assertArrayHasKey('count', $results);
        $this->assertArrayHasKey('results', $results);
        $this->assertGreaterThanOrEqual($results['count'], count($results['results']));
        $this->assertInstanceOf(App\Entities\Event::class, $results['results'][0]);
    }
}