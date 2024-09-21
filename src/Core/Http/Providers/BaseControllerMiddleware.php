<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http\Providers;

use Wepesi\Core\Media;

/**
 *
 */
abstract class BaseControllerMiddleware
{
    /**
     * @var Media
     */
    protected Media $media;

    /**
     *
     */
    public function __construct()
    {
        $this->media = new Media();
    }
}
