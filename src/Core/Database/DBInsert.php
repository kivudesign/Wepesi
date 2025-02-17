<?php

namespace Wepesi\Core\Database;

use PDO;
use Wepesi\Core\Database\Providers\Contracts\DatabaseQueryContracts;
use Wepesi\Core\Database\Providers\DatabaseProviders;

/**
 * Insert Query Object
 * @package Wepesi\Core\Database
 * @template DBInsert of DatabaseQueryContracts
 * @template-implements DatabaseQueryContracts<DBInsert>
 * @template-extends DatabaseProviders<DBInsert>
 */
class DBInsert extends DatabaseProviders implements DatabaseQueryContracts
{
    /**
     * @var array
     */
    private array $_fields;

    /**
     * @param PDO $pdo
     * @param string $table
     */
    public function __construct(PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->_fields = [];
        $this->lastID = 0;
        $this->_error = '';
    }

    /**
     * profile field to be saved
     * @param array $fields
     * @return $this
     */
    public function field(array $fields): DBInsert
    {
        if (count($fields) && !$this->_fields) {
            $field_key_position = 0;
            $keys = array_keys($fields);
            $values = null;
            $trim_key = [];
            foreach ($fields as $ignored) {
                $values .= '? ';
                if (count($fields) > ($field_key_position + 1)) {
                    $values .= ', ';
                }
                //remove white space around the field name
                $trim_key[] = trim($keys[$field_key_position]);
                $field_key_position++;
            }

            $implode_keys = '`' . implode('`,`', $trim_key) . '`';

            $this->_fields = [
                'keys' => $implode_keys,
                'values' => $values,
                'params' => $fields
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
        $this->insert();
        return $this->result;
    }

    /**
     *
     */
    private function insert(): void
    {
        $fields = $this->_fields['keys'];
        $values = $this->_fields['values'];
        $params = $this->_fields['params'];
        $sql = "INSERT INTO $this->table ($fields) VALUES ($values)";
        $this->prepareQueryExecution($sql, $params);
    }

    /**
     * @return int
     */
    public function lastId(): int
    {
        return $this->lastID;
    }
}
