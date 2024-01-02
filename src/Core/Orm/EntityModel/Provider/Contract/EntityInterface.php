<?php

namespace Wepesi\Core\Orm\EntityModel\Provider\Contract;

interface EntityInterface
{
    public function findAll(): array;

    public function findOne(): array;
}