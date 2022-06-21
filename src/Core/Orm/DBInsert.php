<?php

namespace Wepesi\Core\Orm;

class DBInsert
{
    private $table, $_pdo;
    private $_fields;
    private  $_error,
        $_results = false,
        $_count = 0,
        $_lastid;
    function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->_pdo = $pdo;
    }

    //
    function field(array $fields)
    {
        if (count($fields) && !$this->_fields ) {
            $params = $fields;
            $x = 1;
            $keys = array_keys($fields);
            $values = null;
            $_trim_key=[];
            foreach ($fields as $f) {
                $values .= "? ";
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                //remove white space around the field name
                $_trim_key[]=trim($keys[($x-1)]);
                $x++;
            }
            $all_keys=$_trim_key;
            $implode_keys= "`" . implode('`,`', $all_keys) . "`";

            $this->_fields = [
                "keys" => $implode_keys,
                "values" => $values,
                "params" => $params
            ];
        }
        return $this;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return void
     * this module is use to execute sql request
     */
    private function query(string $sql, array $params = [])
    {
        $q = new DBQuery($this->_pdo, $sql, $params);
        $this->_results = $q->result();
        $this->_count = $q->rowCount();
        $this->_error = $q->getError();
        $this->_lastid = $q->lastId();
    }

    /**
     * @return bool
     * use this module to create new record
     */
    private function insert()
    {
        $fields = $this->_fields['keys'];
        $values =  $this->_fields['values'];
        $params = $this->_fields['params'];
        $sql = "INSERT INTO $this->table ($fields) VALUES ($values)";
        return $this->query($sql, $params);
    }

    /**
     * @return bool
     * return result after a request select
     */
    function result()
    {
        $this->insert();
        return $this->_results;
    }
    // return an error status when an error occure while doing an querry
    function error()
    {
        return $this->_error;
    }

    /**
     * @return int
     * return counted rows of a select query
     */
    function count()
    {
        return $this->_count;
    }

    /**
     * @return mixed
     * access the last id record after creating a new record
     */
    function lastId()
    {
        return $this->_lastid;
    }
}