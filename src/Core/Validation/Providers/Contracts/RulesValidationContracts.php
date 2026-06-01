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
     * Generates validation rules based on the provided configuration.
     * 1. Generates the validation rules based on the provided configuration.
     * 2. Returns the generated validation rules.
     *
     * @return array
     */
    public function generate(): array;
}