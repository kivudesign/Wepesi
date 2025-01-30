<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database\Providers\Contracts;

/**
 * @package Wepesi\Core\Database
 * @template T
 */
interface DatabaseConfigContracts
{
    /**
     * @param class-string<T> $host_name
     * @return T
     */
    public function host(string $host_name): DatabaseConfigContracts;

    /**
     * @param class-string<T> $password
     * @return T
     */
    public function password(string $password): DatabaseConfigContracts;

    /**
     * @param class-string<T> $username
     * @return T
     */
    public function username(string $username): DatabaseConfigContracts;

    /**
     * @param class-string<T> $port
     * @return T
     */
    public function port(string $port): DatabaseConfigContracts;

    /**
     * @param class-string<T> $db_name
     * @return T
     */
    public function db(string $db_name): DatabaseConfigContracts;

    /**
     * @return class-string<T>
     */
    public function getDNS(): string;
}
