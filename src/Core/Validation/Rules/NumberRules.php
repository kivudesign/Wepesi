<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Rules;

use Wepesi\Core\Validation\Providers\RulesProvider;

/**
 * Schema number validation
 * validates any format number
 */
final class NumberRules extends RulesProvider
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
    function positive(): NumberRules
    {
        $this->schema[$this->class_name]['positive'] = true;
        return $this;
    }
}
