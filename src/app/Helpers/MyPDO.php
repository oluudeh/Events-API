<?php

namespace App\Helpers;

use PDOStatement;

class MyPDO extends \PDO
{
    public function run(string $query, array $params = []): PDOStatement
    {
        $stmt = $this->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
