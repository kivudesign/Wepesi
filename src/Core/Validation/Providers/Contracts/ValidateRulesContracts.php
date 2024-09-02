<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Providers\Contracts;


/**
 *
 */
interface ValidateRulesContracts extends ValidationContracts
{
    /**
     * @param array $value
     * @return mixed
     */
    public function addError(MessageBuilderContracts $value);

    /**
     * @return array
     */
    public function result(): array;
}