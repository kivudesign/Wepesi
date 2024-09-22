<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Routing\Providers\Contracts;

/**
 * @template T
 */
interface RouterProviders
{
    /**
     * @param string $routes
     * @return void
     */
    public function register(string $routes): void;
}