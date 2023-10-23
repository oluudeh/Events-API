<?php

use App\Helpers\EntityDataMapper;
use PHPUnit\Framework\TestCase;

class EntityDataMapperTest extends TestCase
{
    public function testAssocToEntity(): void
    {
        $data = [
            'event__id' => 1,
            'event__name' => 'Sample Event',
            'city__id' => 2,
            'city__name' => 'Soweto'
        ];

        $event = EntityDataMapper::assocToEntity($data, App\Entities\Event::class);
        $this->assertInstanceOf(App\Entities\Event::class, $event);
        $this->assertEquals($data['event__id'], $event->getId());
        $this->assertInstanceOf(App\Entities\City::class, $event->getCity());
        $this->assertEquals($data['city__name'], $event->getCity()->getName());
    }

    public function testMapResults(): void
    {
        $results = [
            [
                'event__id' => 1,
                'event__name' => 'Sample Event',
                'city__id' => 2,
                'city__name' => 'Soweto'
            ],
            [
                'event__id' => 2,
                'event__name' => 'Sample Event 2',
                'city__id' => 3,
                'city__name' => 'Uli'
            ]
        ];
        
        $events = EntityDataMapper::mapResults($results, App\Entities\Event::class);

        $this->assertCount(2, $events);
        $this->assertInstanceOf(App\Entities\Event::class, $events[0]);
        $this->assertInstanceOf(App\Entities\City::class, $events[1]->getCity());
    }
}
