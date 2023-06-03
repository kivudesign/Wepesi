<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

namespace Wepesi\Core\Orm;

use Wepesi\Core\Config;

/**
 *
 */
class DB
    {
    /**
     * @var DB|null
     */
    private static ?DB $_instance;
    /**
     * @var
     */
    private $queryResult;
    /**
     * @var DBSelect|null
     */
    private ?DBSelect $select_db_query;
    /**
     * @var string|null
     */
    private ?string $_error;
    /**
     * @var array|null
     */
    private ?array $_results;
    /**
     * @var int
     */
    private int  $_lastid;
    /**
     * @var \PDO
     */
    private \PDO $pdoObject;
    /**
     * @var array
     */
    private array $option;
    /**
     * @var int
     */
    private int $_count;

    /**
     *
     */
    private function __construct()
        {
            try {
                $this->initialisation();
                $this->pdoObject = new \PDO("mysql:host=" . Config::get('mysql/host') . ";port=" . Config::get('mysql/port') . ";dbname=" . Config::get('mysql/db').";charset=utf8mb4", Config::get('mysql/username'), Config::get('mysql/password'),$this->option);
            } catch (\PDOException $ex) {
                dumper($ex->getMessage());
            }
        }

        /**
         * Initialise all
         */
        private function initialisation(){
            $this->_results=[];
            $this->option = [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ];
            $this->_count=0;
            $this->_lastid=0;
            self::$_instance=null;
        }

    /**
     * @return DB|null
     */
    static function getInstance(): ?DB
        {
            if(!isset(self::$_instance)){
                self::$_instance=new DB();
            }
            return self::$_instance;
        }

        /**
         * @param string $table_name
         * @param string $actions
         * @return QueryTransactions
         * @throws \Exception
         */
        private function queryOperation(string $table_name, string  $actions): QueryTransactions
        {
            if (strlen($table_name) < 1) {
                throw new \Exception("table name should be a string");
            }
            return new QueryTransactions($this->pdoObject, $table_name, $actions);
        }

        /**
         * @string :$table =>this is the name of the table where to get information
         * this method allows doing a select field from a $table with all the conditions defined ;
         * @throws \Exception
         */
        function get(string $table_name): ?DBSelect
        {
            return $this->select_option($table_name);
        }

        /**
         * @string :$table =>this is the name of the table where to get information
         * this method allow to do a count the number of fields on a $table with all the possible condition
         * @throws \Exception
         */
        function count(string $table_name): DBSelect
        {
            return $this->select_option($table_name, "count");
        }

        /**
         * @string : $table=> this is the name of the table where to get information
         * @string : @action=> this is the type of action tu do while want to do a request
         * @throws \Exception
         */
        private function select_option(string $table_name, string $action = null): DBSelect
        {
            if (strlen($table_name) < 1) {
                throw new \Exception("table name should be a string");
            }
            return $this->queryResult = new DBSelect($this->pdoObject, $table_name, $action);
        }

        /**
         * @param string $table_name
         * @return DBInsert
         */
        function insert(string $table_name): DBInsert
        {
            return $this->queryResult = new DBInsert($this->pdoObject,$table_name);
        }

        /**
         * @param string $table_name
         * @return DBCreateMultiple
         */
        function insertMultiple(string  $table_name): DBCreateMultiple
        {
            return $this->queryResult =new DBCreateMultiple($this->pdoObject, $table_name);
        }

        /**
         * @param string $table  this is the name of the table where to get information
         * @return DBDelete
         * @throws \Exception
         * this method will help delete row data information
         */
        function delete(string $table): DBDelete
        {
            return $this->queryResult = new DBDelete($this->pdoObject,$table);
        }
        //

        /**
         * @param string $table  this is the name of the table where to get information
         * @return DBUpdate
         * @throws \Exception
         * this methode will help update row information of a selected tables
         */
        function update(string $table): DBUpdate
        {
            return $this->queryResult = new DBUpdate($this->pdoObject,$table);
        }
        //

    /**
     * @param $sql
     * @param array $params
     * @return $this
     */
    function query($sql, array $params = []): DB
        {
            $q = new DBQuery($this->pdoObject, $sql, $params);
            $this->_results = $q->result();
            $this->_count = $q->rowCount();
            $this->_error = $q->getError();
            $this->_lastid = $q->lastId();
            return $this;
        }

    /**
     * return the last id after an insert operation query
     * @return int|null
     */
    function lastId(): ?int
        {
            return isset($this->queryResult) ? $this->queryResult->lastId() : $this->_lastid;
        }
        /**
         * return an error status when an error occurs while doing a query
         */
        function error()
        {
            if(isset($this->queryResult)){
                return $this->queryResult->error();
            }else{
                /**
                 * if it was a written query, it will return the error if it exists.
                 * otherwise, it will return false
                 */
                return $this->_error;
            }
        }

        /**
         * @return array|null
         */
        function result(): ?array
        {
            return $this->_results;
        }

        /**
         * @return int
         */
        function rowCount(){
            return isset($this->queryResult) ? $this->queryResult->count() : $this->_count;
        }
    }
