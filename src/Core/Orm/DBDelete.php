<?php

namespace Wepesi\Core\Orm;

use Wepesi\Core\Orm\Provider\DbProvider;
use Wepesi\Core\Orm\Traits\DBWhereCondition;
use Wepesi\Core\Orm\WhereQueryBuilder\WhereBuilder;

/**
 * ORM DELETE QUERY
 */
class DBDelete extends DbProvider
{
    /**
     * @var string
     */
    private string $table;
    /**
     * @var array
     */
    private array $where;
    /**
     * @var array
     */
    private array $_results;
    use DBWhereCondition;

    /**
     * @param \PDO $pdo
     * @param string $table
     */
    function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->where = [];
        $this->_error = '';
        $this->_results = [];
    }

    /**
     * @param array $where
     * @return $this
     */
    function where(WhereBuilder $where_builder): DBDelete
    {
        $this->where = $this->condition($where_builder->generate());
        return $this;
    }

    /**
     * @return array return result after a request select
     * return result after a request select
     */
    function result(): array
    {
        $this->delete();
        return $this->_results;
    }

    /**
     * @return void use this module to delete and existing row record
     * use this module to delete and existing row record
     */
    private function delete(): void
    {
        $where = $this->where['field'] ?? '';
        $params = $this->where['params'] ?? [];
        $sql = "DELETE FROM $this->table $where";
        $this->query($sql, $params);
    }
}