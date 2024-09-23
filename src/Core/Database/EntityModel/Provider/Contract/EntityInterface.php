<?php

namespace Wepesi\Core\Database\EntityModel\Provider\Contract;

interface EntityInterface
{
    public function findAll(): array;

    public function findOne(): array;
}