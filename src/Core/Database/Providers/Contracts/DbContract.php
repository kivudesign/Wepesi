<?php

namespace Wepesi\Core\Database\Providers\Contracts;

/**
 * @package Wepesi\Core\Database
 * @template T
 */
interface DbContract
{
    /**
     * @return string
     */
    function error(): string;

    /**
     * @return array
     */
    function result(): array;

    /**
     * @return int
     */
    function count(): int;
}
