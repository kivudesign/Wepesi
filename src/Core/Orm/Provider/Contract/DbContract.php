<?php

namespace Wepesi\Core\Orm\Provider\Contract;

interface DbContract
{
    function error() : string;
    function result() : array;
    function count() : int;
}