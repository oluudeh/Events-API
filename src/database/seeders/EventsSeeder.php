<?php

namespace Database\Seeders;


use App\Helpers\MyPDO;

class EventsSeeder
{
    public static function run(MyPDO $db)
    {
        $dataFile = __DIR__ . '/data.json';
        $json = file_get_contents($dataFile);
        $events = json_decode($json, true);


        $cities = [];
        $countries = [];

        foreach ($events as $event) {
            $country = $event['country'];

            if (!isset($countries[$country])) {
                $db->run("INSERT INTO country (name) VALUES (:name)", ['name' => $country]);
                $countryId = $db->lastInsertId();
    
                $countries[$country] = $countryId;
            }

            $city = $event['city'];

            if (!isset($cities[$city])) {
                $db->run("INSERT INTO city (name, country_id) VALUES (:name, :country_id)", [
                    'name' => $city,
                    'country_id' => $countries[$country]
                ]);
                $cityId = $db->lastInsertId();
    
                $cities[$city] = $cityId;
            }

            $sql = "
                INSERT INTO event (name, city_id, start_date, end_date)
                VALUES (:name, :city_id, :start_date, :end_date)
            ";

            $db->run($sql, [
                'name' => $event['name'],
                'city_id' => $cities[$city],
                'start_date' => $event['startDate'],
                'end_date' => $event['endDate']
            ]);
        }
    }
}
