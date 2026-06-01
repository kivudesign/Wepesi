<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http\Providers;

use Wepesi\Core\Application;
use Wepesi\Core\Database\Providers\Contracts\WhereBuilderContracts;
use Wepesi\Core\Database\Providers\Contracts\WhereConditionContracts;
use Wepesi\Core\Database\WhereQueryBuilder\WhereBuilder;
use Wepesi\Core\Database\WhereQueryBuilder\WhereConditions;
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
    /**
     * @var Request
     */
    protected Request $request;
    /**
     * @var Response
     */
    protected Response $response;
    /**
     * @var Session
     */
    protected Session $session;
    /**
     * @var WhereBuilderContracts
     */
    protected WhereBuilderContracts $whereBuilder;
    /**
     * @var WhereConditionContracts
     */
    protected WhereConditionContracts $whereCondition;

    /**
     * @return void
     */
    protected function initializeBaseServices(): void
    {
        $this->media = Application::make(Media::class);
        $this->request = Application::make(Request::class);
        $this->response = Application::make(Response::class);
        $this->session = Application::make(Session::class);
        // Entity Manager
        $this->whereBuilder = Application::make(WhereBuilder::class);
    }
}
