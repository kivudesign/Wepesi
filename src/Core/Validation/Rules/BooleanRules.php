<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Rules;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Wepesi\Core\Validation\Providers\RulesProvider;

/**
 * Description of String
 *
 * @author Domeshow
 */
final class BooleanRules extends RulesProvider
{

    /**
     *
     */
    function __construct()
    {
        parent::__construct(__CLASS__);
    }

    /**
     * @return $this
     */
    function isValid(): BooleanRules
    {
        $this->schema[$this->class_name]['isValid'] = true;
        return $this;
    }
}
