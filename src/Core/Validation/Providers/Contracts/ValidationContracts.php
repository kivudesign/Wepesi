<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Providers\Contracts;


/**
 *
 */
interface ValidationContracts
{
    /**
     * @param int $rule
     *
     */
    public function min(int $rule);

    /**
     * @param int $rule
     *
     */
    public function max(int $rule);

    /**
     * Marks a field or parameter as mandatory.
     * Ensures that a value must be provided for this specific field or parameter.
     */
    public function required();
}