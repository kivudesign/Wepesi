<?php

namespace Wepesi\Core\Orm;

use This;
use Wepesi\Core\Config;
use Wepesi\Core\Orm\Traits\QueryExecuter;

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
    private string $_error;
    /**
     * @var array
     */
    private array $_results;
    /**
     * @var int
     */
    private int $lastID;
    /**
     * @var \PDO
     */
    private \PDO $pdoObject;
    /**
     * @var int
     */
    private int $_count;
    /**
     * @var false|mixed
     */
    private $db_name;
    use QueryExecuter;

    /**
     *
     */
    private function __construct()
    {
        try {
            if (!Config::get('mysql/usable')) {
                throw new \Exception('you should authorized user database on config file.');
            }
            $this->initialisation();
            $config = self::getConfig();
            $this->db_name = $config->db;
            $this->pdoObject = new \PDO('mysql:host=' . $config->host . ';port=' . $config->port . ';dbname=' . $this->db_name . ';charset=utf8mb4', $config->username, $config->password);
            $this->pdoObject->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->pdoObject->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdoObject->setAttribute(\PDO::MYSQL_ATTR_FOUND_ROWS, true);
            $this->pdoObject->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @string :$table =>this is the name of the table where to get information
     * this method allow to do a select field from  a $table with all the conditions defined ;
     * @throws \Exception
     */
    public function get(string $table_name): ?DBSelect
    {
        return $this->select_option($table_name);
    }

    /**
     * @string : $table=> this is the name of the table where to get information
     * @string : @action=> this is the type of action tu do while want to do a request
     * @throws \Exception
     */
    private function select_option(string $table_name, string $action = null): DBSelect
    {
        if (strlen($table_name) < 1) {
            throw new \Exception('table name should be a string');
        }
        $this->queryResult = new DBSelect($this->pdoObject, $table_name, $action);
        return $this->queryResult;
    }

    /**
     * Initialise all
     */
    private function initialisation()
    {
        $this->_results = [];
        $this->_count = 0;
        $this->lastID = 0;
        $this->_error = '';
//        self::$_instance = null;
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
     * @param string $table :  this is the name of the table where to get information
     * @return DBDelete
     * @throws \Exception
     * this method will help delete row data information
     */
    public function delete(string $table): DBDelete
    {
        $this->queryResult = new DBDelete($this->pdoObject, $table);
        return $this->queryResult;
    }

    /**
     * @param string $table : this is the name of the table where to get information
     * @return DBUpdate
     * @throws \Exception
     * this methode will help update row information of a selected tables
     */
    public function update(string $table): DBUpdate
    {
        $this->queryResult = new DBUpdate($this->pdoObject, $table);
        return $this->queryResult;
    }
    //

    /**
     * @return int
     */
    public function lastId(): int
    {
        if ($this->queryResult && method_exists($this->queryResult, 'lastId')) $this->lastID = $this->queryResult->lastId();
        return $this->lastID;
    }

    /**
     * @return string|null
     */
    public function error(): string
    {
        if (isset($this->queryResult)) $this->_error = $this->queryResult->error();
        return $this->_error;
    }

    /**
     * @return int
     */
    public function rowCount(): int
    {
        return $this->queryResult->count() ?? $this->_count;
    }

    /**
     * @string :$table =>this is the name of the table where to get information
     * this method allow to do a count the number of field on a $table with all the possible condition
     * @throws \Exception
     */
    public function count(string $table_name): DBSelect
    {
        return $this->select_option($table_name, 'count');
    }

    /**
     *
     * @throws \Exception
     */
    public function transaction(\Closure $callable)
    {
        try {
            $this->convertToInnoDB();
            $this->pdoObject->beginTransaction();
            $callable($this);
            $this->pdoObject->commit();
        } catch (\Exception $ex) {
            if ($this->pdoObject->inTransaction()) {
                $this->pdoObject->rollBack();
            }
            throw $ex;
        }
    }

    /**
     * @throws \Exception
     */
    public function convertToInnoDB()
    {
        try {
            $result = $this->get_db_engine_table();
            foreach ($result as $table) {
                $sql = "ALTER TABLE $table->TABLE_NAME ENGINE=InnoDB";
                $this->query($sql);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param string $engine : default "MyISAM"
     * @return array
     * @throws \Exception
     */

    protected function get_db_engine_table(string $engine = 'MyISAM'): array
    {
        try {
            $params = [$this->db_name, $engine];
            $sql = 'SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? AND `ENGINE` = ?';
            return self::query($sql, $params)->result();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return array|null
     */
    public function result(): array
    {
        return $this->_results;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return $this
     */
    public function query(string $sql, array $params = []): DB
    {
        $q = $this->executeQuery($this->pdoObject, $sql, $params);
        $this->_results = $q['result'];
        $this->_count = $q['count'];
        $this->_error = $q['error'];
        $this->lastID = $q['lastID'];
        return $this;
    }

    /**
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->pdoObject->beginTransaction();
    }

    /**
     * @return mixed
     */
    public function commit()
    {
        return $this->pdoObject->commit();
    }

    /**
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->pdoObject->rollBack();
    }
}
