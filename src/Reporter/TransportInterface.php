<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Reporter;

/**
 * Interface for error event transports
 */
interface TransportInterface
{
    /**
     * Send an error event
     * @param array $event
     * @return bool
     */
    public function send(array $event): bool;
}
