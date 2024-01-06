<?php

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
    private static array $config = [];

    public function __construct()
    {
        self::$config['host'] = $_ENV['DB_HOST'];
        self::$config['port'] = $_ENV['DB_PORT'];
        self::$config['db'] = $_ENV['DB_NAME'];
        self::$config['password'] = $_ENV['DB_USER'];
        self::$config['username'] = $_ENV['DB_PASSWORD'];
    }

    /**
     * Get database connection information's
     * @return object
     */
    protected static function getConfig(): object
    {
        return (object)self::$config;
    }

    /**
     * Set database host name
     * @param string $host_name
     * @return $this
     */
    public function host(string $host_name): DBConfig
    {
        self::$config['host'] = $host_name;
        return $this;
    }

    /**
     * Set database connection user  password
     * @param string $password
     * @return $this
     */
    public function password(string $password): DBConfig
    {
        self::$config['password'] = $password;
        return $this;
    }

    /**
     * Set database connection username
     * @param string $username
     * @return $this
     */
    public function username(string $username): DBConfig
    {
        self::$config['username'] = $username;
        return $this;
    }

    /**
     * set database connection default 3306
     * @param string $port
     * @return $this
     */
    public function port(string $port): DBConfig
    {
        self::$config['port'] = $port;
        return $this;
    }

    /**
     * Set database name to be selected
     * @param string $db_name
     * @return $this
     */
    public function db(string $db_name): DBConfig
    {
        self::$config['db'] = $db_name;
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