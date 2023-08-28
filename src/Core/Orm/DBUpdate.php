<?php

namespace Wepesi\Core\Orm;

use Wepesi\Core\Orm\Provider\DbProvider;
use Wepesi\Core\Orm\Traits\DBWhereCondition;
use Wepesi\Core\Orm\WhereQueryBuilder\WhereBuilder;

/**
 *
 */
class DBUpdate extends DbProvider
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
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->_fields = [];
    }

    /**
     * @param WhereBuilder $where_builder
     * @return $this
     */
    public function where(WhereBuilder $where_builder): DBUpdate
    {
        $this->where = $this->condition($where_builder);
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
            foreach ($fields as $field) {
                $values .= '? ';
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                //remove white space around the collum name
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
    private function update()
    {
        $where = $this->where['field'] ?? null;
        $where_params = $this->where['params'] ?? [];
        $fields = $this->_fields['keys'];
        $field_params = $this->_fields['params'] ?? [];
        $params = array_merge($field_params, $where_params);
        //generate the sql query to be execute
        $sql = "UPDATE $this->table SET $fields  $where";
        $this->query($sql, $params);
    }
}
