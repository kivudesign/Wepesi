<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Orm;

/**
 * Provide DataBase connection connection configurations.
 * By default set up from `.env`.
 */
class DBConfig
{
    /**
     * @var array
     */
    protected array $dbConfig = [];
    /*
     * Get Default configuration
     * @return array
     */
    private function defaultConfig (): array
    {
        return  [
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'db' => $_ENV['DB_NAME'],
            'password' => $_ENV['DB_USER'],
            'username' => $_ENV['DB_PASSWORD']
        ];
    }
    /**
     * Get database connection information's
     * @return object
     */
    protected function getDBConfig(): object
    {
        return (object) (count($this->dbConfig)>0 ? $this->dbConfig : $this->defaultConfig());
    }

    /**
     * Set database host name
     * @param string $host_name
     * @return $this
     */
    public function host(string $host_name): DBConfig
    {
        $this->dbConfig['host'] = $host_name;
        return $this;
    }

    /**
     * Set database connection user  password
     * @param string $password
     * @return $this
     */
    public function password(string $password): DBConfig
    {
        $this->dbConfig['password'] = $password;
        return $this;
    }

    /**
     * Set database connection username
     * @param string $username
     * @return $this
     */
    public function username(string $username): DBConfig
    {
        $this->dbConfig['username'] = $username;
        return $this;
    }

    /**
     * set database connection default 3306
     * @param string $port
     * @return $this
     */
    public function port(string $port): DBConfig
    {
        $this->dbConfig['port'] = $port;
        return $this;
    }

    /**
     * Set database name to be selected
     * @param string $db_name
     * @return $this
     */
    public function db(string $db_name): DBConfig
    {
        $this->dbConfig['db'] = $db_name;
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|void
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }
    }
}