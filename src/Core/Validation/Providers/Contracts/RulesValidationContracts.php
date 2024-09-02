<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Providers\Contracts;


/**
 *
 */
interface RulesValidationContracts extends ValidationContracts
{

    /**
     * @return mixed
     */
    public function generate();
}