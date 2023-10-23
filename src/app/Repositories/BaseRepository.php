<?php

namespace App\Repositories;


use App\Helpers\EntityDataMapper;
use App\Helpers\MyPDO;
use App\Helpers\StringUtils;
use PDO;

abstract class BaseRepository
{
    protected $table;
    

    public function __construct(
        protected MyPDO $db,
        protected string $entity,
    )
    {
        $this->table = call_user_func([$entity, 'tableName']);
    }

    /**
     * Build SQL WHERE clause from list of conditions.
     */
    protected function buildWhereClause(array $conditions): string
    {
        $clauses = array_map(fn ($condition) => $this->parseCondition($condition), $conditions);

        return implode(" AND ", $clauses);
    }

    protected function parseCondition(array $condition): string
    {
        if (isset($condition['group'])) {
            $conditions = array_map(fn ($cond) => $this->parseCondition($cond), $condition['group']);
            return "( " . implode(" {$condition['glue']} ", $conditions) . ")";
        }
        $placeholder = $this->makePlaceholder($condition['column']);
        return "{$condition['column']} {$condition['operator']} :{$placeholder}";
    }

    protected function makePlaceholder(string $column): string
    {
        $col = str_replace('.', '_', $column);
        return StringUtils::camelToSnakeCase($col);
    }

    /**
     * Collect placeholder values
     */
    protected function buildValues(array $conditions, array &$values)
    {
        $values = [];
        foreach ($conditions as $condition) {
            if (isset($condition['group'])) {
                $this->buildValues($condition['group'], $values);
            } else {
                $placeholder = $this->makePlaceholder($condition['column']);
                $values[$placeholder] = $condition['value'];
            }
        }
    }

    /**
     * Fetched paged results from DB table(s).
     *
     * @param string|string[] $columns Table columns to select
     * @param array $conditions Where conditions
     * @param array $joins SQL joins
     * @param string $orderBy
     * @param int $page
     * @param int $limit
     *
     * @return array Keyed by 'count' and 'results'
     */
    public function paginate(
        array $columns,
        array $conditions = [],
        array $joins = [],
        string $orderBy = '',
        int $page = 1,
        int $limit = 10
    )
    {
        $cols = join(
            ", ",
            array_map(fn ($col) => "{$col} AS " . EntityDataMapper::makeAlias($col), $columns)
        );

        $sql = " SELECT {$cols} FROM {$this->table}";

        $countSql = "SELECT COUNT(*) AS count FROM {$this->table}";

        if ($joins) {
            foreach($joins as $join) {
                $joinStr = " {$join['type']} JOIN {$join['table']} ON {$join['condition']} \n";
                $sql .= $joinStr;
                $countSql .= $joinStr;
            }
        }

        $values = [];

        if ($conditions) {
            $this->buildValues($conditions, $values);

            $whereClause = $this->buildWhereClause($conditions);
            
            $sql .= " WHERE {$whereClause} ";
            $countSql .= " WHERE {$whereClause} \n";
        }

        if ($orderBy) {
            list($column, $order) = explode(":", $orderBy);
            $sql .= " ORDER BY {$column} {$order}";
        }

        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        $countStmt = $this->db->run($countSql, $values);
        $count = $countStmt->fetchColumn();

        $stmt = $this->db->run($sql, $values);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $entities = EntityDataMapper::mapResults($results, $this->entity);

        return [
            'count' => $count,
            'results' => $entities,
        ];
    }

}
