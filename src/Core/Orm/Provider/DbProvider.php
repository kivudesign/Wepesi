<?php

namespace Wepesi\Core\Orm\Provider;

use Wepesi\Core\Orm\Provider\Contract\DbContract;
use Wepesi\Core\Orm\Traits\QueryExecuter;

/**
 *
 */
abstract class DbProvider implements DbContract
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
     * @var \PDO
     */
    protected \PDO $pdo;
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
    use QueryExecuter;

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

    abstract function result(): array;

    /**
     * Execute a sql query
     * @param string $sql
     * @param array $values
     * @return void
     */
    protected function query(string $sql, array $values): void
    {
        $q = $this->executeQuery($this->pdo, $sql, $values);
        $this->result = $q['result'];
        $this->lastID = $q['lastID'] ?? 0;
        $this->_count = $q['count'] ?? 0;

        if ($q['error'] !== '') {
            $this->_error = $q['error'];
            $this->result = ['exception' => $q['error']];
        }
    }
}