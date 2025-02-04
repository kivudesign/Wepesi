<?php

namespace Wepesi\Core\Database\Providers\Contracts;

/**
 * @package Wepesi\Core\Database
 * @template T
 */
interface EntityContracts
{
    /**
     * @return array
     */
    public function findAll(): array;

    /**
     * @return array
     */
    public function findOne(): array;
}
