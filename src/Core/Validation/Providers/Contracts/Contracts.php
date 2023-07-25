<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Providers\Contracts;


/**
 *
 */
interface Contracts
{
    /**
     * @param int $rule
     * @return mixed
     */
    public function min(int $rule);

    /**
     * @param int $rule
     * @return mixed
     */
    public function max(int $rule);

    /**
     * @return mixed
     */
    public function required();
}