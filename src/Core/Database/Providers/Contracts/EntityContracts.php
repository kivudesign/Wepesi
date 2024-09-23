<?php

namespace Wepesi\Core\Database\Providers\Contracts;

interface EntityContracts
{
    public function findAll(): array;

    public function findOne(): array;
}