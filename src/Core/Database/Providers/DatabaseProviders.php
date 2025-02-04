<?php

namespace Wepesi\Core\Database\Providers;

use PDO;
use Wepesi\Core\Database\Providers\Contracts\DbContract;
use Wepesi\Core\Database\Traits\QueryExecute;

/**
 * @package Wepesi\Core\Database
 * @template DatabaseProviders of DbContract
 * @template-implements DbContract<DatabaseProviders>
 */
abstract class DatabaseProviders implements DbContract
{
    /**
     * @var string
     */
    protected string $table;
    /**
     * @var string
     */
    protected string $_error = '';
    /**
     * @var PDO
     */
    protected PDO $pdo;
    /**
     * @var array
     */
    protected array $result = [];
    /**
     * @var int
     */
    protected int $lastID = 0;
    /**
     * @var int
     */
    protected int $_count = 0;
    /**
     * @var array
     */
    protected array $include_object;
    protected bool $isCount = false;
    use QueryExecute;

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->_error;
    }

    /**
     * @return int
     * return counted rows of a select query
     */
    public function count(): int
    {
        return $this->_count;
    }

    /**
     * @return array
     */
    abstract function result(): array;

    /**
     * Execute a sql query
     * @param string $sql
     * @param array $values
     * @param int $last_id
     * @param bool $is_query_string
     * @return void
     */
    protected function prepareQueryExecution(string $sql, array $values, int $last_id = -1, bool $is_query_string = false): void
    {
        $q = $this->executeQuery($this->pdo, $sql, $values, $last_id, $is_query_string);
        $this->result = $q['result'];
        $this->lastID = $q['lastID'] ?? 0;
        $this->_count = $q['count'] ?? 0;

        if ($q['error'] !== '') {
            $this->_error = $q['error'];
            $this->result = ['exception' => $q['error']];
        }
    }
}
