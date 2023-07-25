<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Providers\Contracts;


/**
 *
 */
interface SchemaContracts extends Contracts
{

    /**
     * @return mixed
     */
    public function generate();
}