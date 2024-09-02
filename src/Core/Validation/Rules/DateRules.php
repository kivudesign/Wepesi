<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Rules;

use Wepesi\Core\Validation\Providers\RulesProvider;

/**
 * Schema datetime
 *
 */
final class DateRules extends RulesProvider
{
    /**
     *
     */
    function __construct()
    {
        parent::__construct(__CLASS__);
    }

    /**
     * @param string $rule
     * @return DateRules
     */
    public function min($rule): DateRules
    {
        $this->schema[$this->class_name]['min'] = $rule;
        return $this;
    }

    /**
     * @param string $rule
     * @return $this
     */
    public function max($rule): DateRules
    {
        $this->schema[$this->class_name]['max'] = $rule;
        return $this;
    }

    /**
     * @return $this
     */
    function now(): DateRules
    {
        $this->schema[$this->class_name]["now"] = true;
        return $this;
    }

    /**
     * @return $this
     */
    function today(): DateRules
    {
        $this->schema[$this->class_name]["today"] = true;
        return $this;
    }
}