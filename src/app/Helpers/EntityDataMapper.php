<?php

namespace App\Helpers;

use App\Entities\BaseEntity;

/**
 * Entity helper class for mapping SQL query assoc results to Entity classes.
 */
class EntityDataMapper
{

    /**
     * Map SQL query assoc result to specified Entity class.
     * @param array $data An associative array containing SQL query result.
     * @param string $entity The resulting Entity class name. Must by a subclass of BaseEntity.
     * @return Entity The specified Entity class in param 2
     */
    public static function assocToEntity(array $data, string $entity): BaseEntity
    {
        $data = self::transformKeys($data, $entity);

        $class = new \ReflectionClass($entity);
        $instance = new $entity();

        foreach ($data as $key => $value) {

            // converts snake_case column name to camelCase Entity property. Example: start_date to startDate
            $prop = StringUtils::snakeToCamelCase($key);

            $methodName = "set" . ucfirst($prop);

            if ($class->hasMethod($methodName)) {
                $method = $class->getMethod($methodName);
                $param = $method->getParameters()[0];
                $paramType = $param->getType();
                
                if ($paramType->isBuiltin()) {
                    $instance->$methodName($value);
                } else {
                    $paramClass = $paramType->getName();
                    $paramReflector = new \ReflectionClass($paramClass);

                    // map relationships to other Entities
                    if ($paramReflector->isSubclassOf(BaseEntity::class)) {
                        $instance->$methodName(self::assocToEntity($value, $paramClass));
                    } else {
                        $paramInstance = new $paramClass($value);
                        $instance->$methodName($paramInstance);
                    }
                }
            }
        }

        return $instance;
    }

    /**
     * Map array of results to array of Entities
     * @param array $results Array of assoc SQL query results.
     * @param string $entity The resulting Entity class name. Must by a subclass of BaseEntity.
     * @return Entity[] An array of the specified Entity class in param 2
     */
    public static function mapResults(array $results, string $entity): array
    {
        return array_map(
            fn ($result) => self::assocToEntity($result, $entity),
            $results
        );
    }

    /**
     * Restructure SQL assoc results to represent entity relationship structure.
     * This is based on the format of aliases created by EntityDataMapper::makeAlias()
     * Example:
     *  $data = [
     *      'event__id' => 1,
     *      'event__name' => 'Sample Event',
     *      'city__id' => 2,
     *      'city__name' => 'Soweto'
     *  ];
     *  $entity = App\Entities\Event;
     *
     * Becomes
     * [
     *    'id' => 1,
     *    'name' => 'Sample Event',
     *    'city' => [
     *       'id' => 2,
     *       'name' => 'Soweto'
     *    ]
     * ]
     *
     * @param array $data An associative array containing SQL query result.
     * 
     * 
     * @see EntityMappper::makeAlias()
     * @todo Add support for multi-level relationships
     */
    private static function transformKeys(array $data, string $entity): array
    {
        $baseTable = call_user_func([$entity, 'tableName']);

        $newData = [];
        foreach ($data as $key => $value) {
            if (str_contains($key,"__")) {
                list($newKey, $valueKey) = explode("__", $key, 2);
                if ($newKey === $baseTable) {
                    $newData[$valueKey] = $value;
                } else {
                    if (!isset($newData[$newKey])) {
                        $newData[$newKey] = [];
                    }
                    $newData[$newKey][$valueKey] = $value;
                }
            } else {
                $newData[$key] = $value;
            }
        }
        return $newData;
    }

    /**
     * Create SQL SELECT alias from column name. Particularly for JOINS so city.name becomes city__name.
     * This is used to create relationship structure when mapping results to Entity
     *
     * @param string $column Column name
     * @return string
     *
     * @see EntityDataMapper::transformKeys()
     */
    public static function makeAlias(string $column): string
    {
        $col = str_replace('.', '__', $column);
        return StringUtils::camelToSnakeCase($col);
    }
}
