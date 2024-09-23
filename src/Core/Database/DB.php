<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database;

use Closure;
use PDO;
use PDOException;
use Wepesi\Core\Config;
use Wepesi\Core\Database\Traits\QueryExecuter;
use Wepesi\Core\Exceptions\DatabaseException;

/**
 *
 */
class DB extends DBConfig
{
    /**
     * @var DB
     */
    private static DB $_instance;
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
    private ?string $request_error;
    /**
     * @var array
     */
    private array $request_results;
    /**
     * @var int
     */
    private int $request_lastID;
    /**
     * @var PDO
     */
    private PDO $pdoObject;
    /**
     * @var int
     */
    private int $result_count;
    /**
     * @var string
     */
    private string $db_name;
    use QueryExecuter;

    /**
     *
     * @throws DatabaseException|PDOException
     */
    private function __construct()
    {
        try {
            if (!Config::get('mysql/usable')) {
                throw new DatabaseException('you should authorized user database on config file.');
            }
            $this->flush();
            $config = $this->getDBConfig();
            $this->db_name = $config->db;
            $this->pdoObject = new PDO('mysql:host=' . $config->host . ';port=' . $config->port . ';dbname=' . $this->db_name . ';charset=utf8mb4', $config->username, $config->password);
            $this->pdoObject->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->pdoObject->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdoObject->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
            $this->pdoObject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * Select field from a $table_name with all the conditions defined ;
     * @param string $table_name provide the table name
     *
     * @throws DatabaseException
     */
    public function get(string $table_name): ?DBSelect
    {
        return $this->select_option($table_name);
    }

    /**
     * @param string $table_name table where to get information
     * @param string|null $action action type to do while want to do a request [select, count]
     * @return DBSelect
     * @throws DatabaseException
     */
    private function select_option(string $table_name, string $action = null): DBSelect
    {
        if (strlen(trim($table_name)) < 1) {
            throw new DatabaseException('table name should be a string');
        }
        $this->queryResult = new DBSelect($this->pdoObject, $table_name, $action);
        return $this->queryResult;
    }

    /**
     * Reset all request results
     */
    private function flush(): void
    {
        $this->request_results = [];
        $this->result_count = 0;
        $this->request_lastID = -1;
        $this->request_error = '';
    }

    /**
     * @return DB
     */
    static function getInstance(): DB
    {

        if (!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    /**
     * @param string $table_name
     * @return DBInsert
     */
    public function insert(string $table_name): DBInsert
    {
        $this->queryResult = new DBInsert($this->pdoObject, $table_name);
        return $this->queryResult;
    }

    /**
     * Delete row data information from a table
     * @param string $table :  this is the name of the table where to get information
     * @return DBDelete
     * @throws DatabaseException     *
     */
    public function delete(string $table): DBDelete
    {
        $this->queryResult = new DBDelete($this->pdoObject, $table);
        return $this->queryResult;
    }

    /**
     * Update row information of a selected tables
     * @param string $table this is the name of the table where to get information
     * @return DBUpdate
     */
    public function update(string $table): DBUpdate
    {
        $this->queryResult = new DBUpdate($this->pdoObject, $table);
        return $this->queryResult;
    }

    /**
     * @return int
     */
    public function lastId(): int
    {
        if ($this->queryResult && method_exists($this->queryResult, 'lastId')) $this->request_lastID = $this->queryResult->lastId();
        return $this->request_lastID;
    }

    /**
     * @return string
     */
    public function error(): string
    {
        if (isset($this->queryResult)) $this->request_error = $this->queryResult->error();
        return $this->request_error;
    }

    /**
     * @return int
     */
    public function rowCount(): int
    {
        return $this->queryResult ? $this->queryResult->count() : $this->result_count;
    }

    /**
     * Count the number of items on a $table_name with all the possible condition
     * @param string $table_name table where to get information
     * @return DBSelect
     * @throws DatabaseException
     */
    public function count(string $table_name): DBSelect
    {
        return $this->select_option($table_name, 'count');
    }

    /**
     * Start transaction and execute your own code
     * @throws DatabaseException
     */
    public function transaction(Closure $callable): void
    {
        try {
            $this->convertToInnoDB();
            $this->pdoObject->beginTransaction();
            $callable($this);
            $this->pdoObject->commit();
        } catch (DatabaseException $ex) {
            if ($this->pdoObject->inTransaction()) {
                $this->pdoObject->rollBack();
            }
            throw $ex;
        }
    }

    /**
     * Convert all tables to InnoDB
     * @throws DatabaseException
     */
    public function convertToInnoDB()
    {
        try {
            $result = $this->getDBEngineTable();
            foreach ($result as $table) {
                $sql = "ALTER TABLE $table->TABLE_NAME ENGINE=InnoDB";
                $this->query($sql);
            }
        } catch (DatabaseException $ex) {
            throw $ex;
        }
    }

    /**
     * Get all table names with engine type default MyISAM
     * @param string $engine default "MyISAM"
     * @return array
     * @throws DatabaseException
     */

    protected function getDBEngineTable(string $engine = 'MyISAM'): array
    {
        try {
            $params = [$this->db_name, $engine];
            $sql = 'SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? AND `ENGINE` = ?';
            return self::query($sql, $params)->result();
        } catch (DatabaseException $ex) {
            throw $ex;
        }
    }

    /**
     * Get all request results
     * @return array
     */
    public function result(): array
    {
        return $this->request_results;
    }

    /**
     * Execute your own sql query
     * @param string $sql
     * @param array $params
     * @return $this
     */
    public function query(string $sql, array $params = []): DB
    {
        $q = $this->executeQuery($this->pdoObject, $sql, $params, -1, true);
        $this->request_results = $q['result'];
        $this->result_count = $q['count'];
        $this->request_error = $q['error'];
        $this->request_lastID = $q['lastID'];
        return $this;
    }

    /**
     * Start database transaction
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->pdoObject->beginTransaction();
    }

    /**
     * Commit transaction while transaction started success
     * @return bool
     */
    public function commit(): bool
    {
        return $this->pdoObject->commit();
    }

    /**
     * Rollback transaction while transaction started failed
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->pdoObject->rollBack();
    }
}
