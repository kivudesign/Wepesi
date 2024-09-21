<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http\Providers\Contracts;

/**
 *
 */
interface RequestContract
{
    /**
     * @return RequestContract
     */
    public static function createFromGlobals(): RequestContract;
}
