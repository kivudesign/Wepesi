<?php

namespace Wepesi\Core\Validation;

interface IValidation
{
    function min();
    function max();
    function required();
    function check();
}