<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database;

use Wepesi\Core\Database\Providers\Contracts\DatabaseConfigContracts;

/**
 *
 * Provide DataBase connection connection configurations.
 * By default set up from `.env`.
 * @package Wepesi\Core\Database
 * @template DatabaseConfig of DatabaseConfigContracts
 * @template-implements DatabaseConfigContracts<DatabaseConfig>
 */
class DatabaseConfig implements DatabaseConfigContracts
{
    /**
     * @var array
     */
    protected static array $db_config = [];

    /**
     * Get database connection information's
     * @return object|null
     */
    public function getDBConfig(): ?object
    {
        if (count(self::$db_config) > 0) {
            return (object)self::$db_config;
        }
        return null;
    }

    /**
     * Set database host name
     * @param string $host_name database host name default 127.0.0.1
     * @return DatabaseConfigContracts
     */
    public function host(string $host_name): DatabaseConfigContracts
    {
        return $this->setConfig('host', $host_name);
    }

    private function setConfig(string $key, string $value): DatabaseConfigContracts
    {
        self::$db_config[$key] = $value;
        return $this;
    }

    /**
     * Set database connection user  password
     * @param string $password database password
     * @return DatabaseConfigContracts
     */
    public function password(string $password): DatabaseConfigContracts
    {
        return $this->setConfig('password', $password);
    }

    /**
     * Set database connection username
     * @param string $username database username
     * @return $this
     */
    public function username(string $username): DatabaseConfigContracts
    {
        return $this->setConfig('username', $username);
    }

    /**
     * set database connection default 3306
     * @param string $port database port default 3306
     * @return DatabaseConfigContracts
     */
    public function port(string $port): DatabaseConfigContracts
    {
        return $this->setConfig('port', $port);
    }

    /**
     * Set database name to be selected
     * @param string $db_name database name
     * @return DatabaseConfigContracts
     */
    public function db(string $db_name): DatabaseConfigContracts
    {
        return $this->setConfig('db', $db_name);
    }

    /**
     * Get DataBase connection string
     * @return class-string<DatabaseConfig>
     */
    public function getDNS(): string
    {
        return sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', self::$db_config['host'], self::$db_config['port'], self::$db_config['db']);
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
