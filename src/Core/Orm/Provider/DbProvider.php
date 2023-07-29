<?php

namespace Wepesi\Core\Orm\Provider;

use Wepesi\Core\Orm\Provider\Contract\DbContract;
use Wepesi\Core\Orm\Traits\QueryExecuter;

/**
 *
 */
abstract class DbProvider Implements DbContract
{
    /**
     * @var string
     */
    protected string $_error = '';
    /**
     * @var \PDO
     */
    protected \PDO $pdo ;
    /**
     * @var array
     */
    protected array $result = [] ;
    /**
     * @var int
     */
    protected int $lastID = 0 ;
    /**
     * @var int
     */
    protected int $_count = 0;

    use QueryExecuter;

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->_error;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return void
     * this module is use to execute sql request
     */
    protected function query(string $sql, array $values)
    {
        $q = $this->executeQuery($this->pdo, $sql, $values);
        $this->result = $q['result'];
        $this->_error = $q['error'] ?? '';
        $this->lastID = $q['lastID'] ?? 0;
        $this->_count = $q['count'] ?? 0;
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
}