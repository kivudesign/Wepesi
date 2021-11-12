<?php

namespace Wepesi\App\Core;

interface IValidation
{
    function min();
    function max();
    function required();
    function check();
}