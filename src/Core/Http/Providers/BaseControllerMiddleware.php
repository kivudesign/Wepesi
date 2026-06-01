<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http\Providers;

use Wepesi\Core\Application;
use Wepesi\Core\Http\Request;
use Wepesi\Core\Http\Response;
use Wepesi\Core\Media;
use Wepesi\Core\Session;

/**
 *
 */
abstract class BaseControllerMiddleware
{
    /**
     * @var Media
     */
    protected Media $media;
    protected Request $request;
    protected Response $response;
    protected Session $session;

    protected function initializeBaseServices(): void
    {
        $this->media = Application::make(Media::class);
        $this->request = Application::make(Request::class);
        $this->response = Application::make(Response::class);
        $this->session = Application::make(Session::class);
    }
}
