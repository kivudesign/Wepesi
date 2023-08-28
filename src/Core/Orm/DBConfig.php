<?php

namespace Wepesi\Core\Orm;

/**
 *
 */
class DBConfig
{
    /**
     * @var array
     */
    private static array $config = [];

    /**
     * @return object
     */
    protected static function getConfig(): object
    {
        return (object)self::$config;
    }

    /**
     * @param string $host_name
     * @return $this
     */
    public function host(string $host_name): DBConfig
    {
        self::$config['host'] = $host_name;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function password(string $password): DBConfig
    {
        self::$config['password'] = $password;
        return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function username(string $username): DBConfig
    {
        self::$config['username'] = $username;
        return $this;
    }

    /**
     * @param string $port
     * @return $this
     */
    public function port(string $port): DBConfig
    {
        self::$config['port'] = $port;
        return $this;
    }

    /**
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