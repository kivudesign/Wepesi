<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Validation\Schema;
use Wepesi\Core\Validation\Validate;

abstract class MiddleWare
{
    protected Validate $validate;
    protected Schema $schema;

    public function __construct(){
        $this->schema = new Schema();
        $this->validate = new Validate();
    }
}