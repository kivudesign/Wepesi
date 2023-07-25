<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Validation\Schema;
use Wepesi\Core\Validation\Validate;

class MiddleWare
{
    protected Validate $validate;
    protected Schema $schema;
}