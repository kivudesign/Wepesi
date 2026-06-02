<?php
/*
 * Copyright (c) 2026. Wepesi Dev Framework
 */

namespace Wepesi\Core\Component\Providers\Contracts;

/**
 * @package Wepesi\Core\Component
 * @template T
 */
interface ComponentContracts
{
    /**
     * Build HTML component UI content
     * 
     * @param array $data information to be displayed
     * 
     * @return string 
     * 
     */
    public function render(array $data): string;
}
