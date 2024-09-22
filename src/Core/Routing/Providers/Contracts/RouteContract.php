<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Routing\Providers\Contracts;

/**
 * @template T
 */
interface RouteContract
{
    public function call();
}