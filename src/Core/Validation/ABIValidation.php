<?php

namespace Wepesi\Core\Validation;

abstract class ABIValidation implements IValidation
{

    private array $_errors=[];
    abstract function min();

    abstract function max();

    abstract function required();

    function check():array{
        return  $this->_errors;
    }
    protected function addError(array $value):array{
        return $this->_errors[]=$value;
    }

}