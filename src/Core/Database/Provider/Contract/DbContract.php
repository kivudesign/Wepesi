<?php

namespace Wepesi\Core\Database\Provider\Contract;

interface DbContract
{
    function error(): string;

    function result(): array;

    function count(): int;
}