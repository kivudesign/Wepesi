<?php

namespace Wepesi\Core\Database;

use PDO;
use Wepesi\Core\Database\Providers\Contracts\DatabaseQueryContracts;
use Wepesi\Core\Database\Providers\Contracts\WhereBuilderContracts;
use Wepesi\Core\Database\Providers\DatabaseProviders;
use Wepesi\Core\Database\Traits\DBWhereCondition;
use Wepesi\Core\Database\WhereQueryBuilder\WhereBuilder;

/**
 * Update Query Object
 * @package Wepesi\Core\Database
 * @template DBUpdate of DatabaseQueryContracts
 * @template-implements DatabaseQueryContracts<DBUpdate>
 * @template-extends DatabaseProviders<DBUpdate>
 */
class DBUpdate extends DatabaseProviders implements DatabaseQueryContracts
{
    /**
     * @var array
     */
    private array $where;
    /**
     * @var array
     */
    private array $_fields;
    /**
     * @var array
     */
    private array $_results;
    use DBWhereCondition;

    /**
     * @param PDO $pdo
     * @param string $table
     */
    public function __construct(PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->_fields = [];
    }

    /**
     * @param WhereBuilderContracts|array $where_builder
     * @return $this
     */
    public function where(WhereBuilderContracts|array $where_builder): DBUpdate
    {
        $this->where = $this->getCondition($where_builder);
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function field(array $fields = []): DBUpdate
    {
        if (count($fields) && !$this->_fields) {
            $keys = $fields;
//                $values = null;
            $params = $keys;
            $x = 1;
            $keys = array_keys($fields);
            $values = null;
            $_trim_key = [];
            foreach ($fields as $ignored) {
                $values .= '? ';
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                //remove white space around the column name
                $_trim_key[] = trim($keys[($x - 1)]);
                $x++;
            }
            $keys = $_trim_key;
            //
            $implode_keys = '`' . implode('`= ?,`', $keys) . '`';
            $implode_keys .= '=?';
            //
            $this->_fields = [
                'keys' => $implode_keys,
                'values' => $values,
                'params' => $params
            ];
        }
        return $this;
    }

    /**
     * @return array
     * return result after a request select
     */
    public function result(): array
    {
        $this->update();
        return $this->result;
    }

    /**
     *
     */
    private function update(): void
    {
        $where = $this->where['field'] ?? null;
        $where_params = $this->where['params'] ?? [];
        $fields = $this->_fields['keys'];
        $field_params = $this->_fields['params'] ?? [];
        $params = array_merge(array_values($field_params), array_values($where_params));
        //generate the SQL query to be executed
        $sql = "UPDATE $this->table SET $fields  $where";
        $this->prepareQueryExecution($sql, $params);
    }
}
