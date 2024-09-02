<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation;

use Wepesi\Core\Validation\Rules\ArrayRules;
use Wepesi\Core\Validation\Rules\BooleanRules;
use Wepesi\Core\Validation\Rules\DateRules;
use Wepesi\Core\Validation\Rules\NumberRules;
use Wepesi\Core\Validation\Rules\StringRules;

/**
 *
 */
final class Rules
{

    /**
     * @return true[]
     */
    function any(): array
    {
        return ['any' => true];
    }

    /**
     * @return StringRules
     */
    public function string(): StringRules
    {
        return new StringRules();
    }

    /**
     * @return NumberRules
     */
    public function number(): NumberRules
    {
        return new NumberRules();
    }

    /**
     * @return DateRules
     */
    public function date(): DateRules
    {
        return new DateRules();
    }

    /**
     * @return BooleanRules
     */
    public function boolean(): BooleanRules
    {
        return new BooleanRules();
    }

    /**
     * @return ArrayRules
     */
    public function array(): ArrayRules
    {
        return new ArrayRules();
    }
    // TODO add support for file validation
}