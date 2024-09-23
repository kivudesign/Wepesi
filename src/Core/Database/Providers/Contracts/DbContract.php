<?php

namespace Wepesi\Core\Database\Providers\Contracts;

interface DbContract
{
    function error(): string;

    function result(): array;

    function count(): int;
}