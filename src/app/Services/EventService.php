<?php

namespace App\Services;

use App\Repositories\EventRepository;
use DI\Attribute\Injectable;

#[Injectable]
class EventService
{
    public function __construct(
        private EventRepository $repository
    )
    {}

    public function fetchEvents(string $term = '', string $date = '', int $page = 1, int $size = 10)
    {
        $columns = [
            'event.id',
            'event.name',
            'event.start_date',
            'event.end_date',
            'city.name',
            'country.name'
        ];

        $conditions = [];
        if ($term) {
            $conditions[] = [
                'group' => [
                    ['column' => 'city.name', 'operator' => 'LIKE', 'value' => "%{$term}%"],
                    ['column' => 'country.name', 'operator' => 'LIKE', 'value' => "%{$term}%"],
                ],
                'glue' => 'OR'
            ];
        }

        if ($date) {
            $conditions[] = ['column' => 'event.start_date', 'operator' => '<=', 'value' => "$date"];
            $conditions[] = ['column' => 'event.end_date', 'operator' => '>=', 'value' => $date];
        }

        $joins = [
            ['type' => 'INNER', 'table' => 'city', 'condition' => 'event.city_id = city.id'],
            ['type' => 'INNER', 'table' => 'country', 'condition' => 'city.country_id = country.id'],
        ];

        $results = $this->repository->paginate(
            columns: $columns,
            conditions: $conditions,
            joins: $joins,
            orderBy: 'event.start_date:ASC',
            page: $page,
            limit: $size,
        );

        $data = array_map(fn ($result) => $result->toArray(), $results['results']);
        $total = $results['count'];
        $totalPages = ceil($total / $size);

        return compact('data', 'total', 'page', 'size', 'totalPages');
    }
}
