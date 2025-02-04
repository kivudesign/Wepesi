<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database\Providers\Contracts;

use Closure;
use Wepesi\Core\Database\DBDelete;
use Wepesi\Core\Database\DBInsert;
use Wepesi\Core\Database\DBSelect;
use Wepesi\Core\Database\DBUpdate;

/**
 * @package Wepesi\Core\Database
 * @template T
 */
interface DatabaseContracts
{
    /**
     * @param string $table_name
     * @return DBSelect|null
     */
    public function get(string $table_name): ?DBSelect;

    /**
     * @param string $table_name
     * @return DBInsert
     */
    public function insert(string $table_name): DBInsert;

    /**
     * @param string $table
     * @return DBDelete
     */
    public function delete(string $table): DBDelete;

    /**
     * @param string $table
     * @return DBUpdate
     */
    public function update(string $table): DBUpdate;

    /**
     * @return int
     */
    public function lastId(): int;

    /**
     * @return string
     */
    public function error(): string;

    /**
     * @return int
     */
    public function rowCount(): int;

    /**
     * @param string $table_name
     * @return DBSelect
     */
    public function count(string $table_name): DBSelect;

    /**
     * @param Closure $callable
     * @return void
     */
    public function transaction(Closure $callable): void;

    /**
     * @return array
     */
    public function result(): array;

    /**
     * @param string $sql
     * @param array $params
     * @return DatabaseContracts
     */
    public function query(string $sql, array $params = []): DatabaseContracts;
}