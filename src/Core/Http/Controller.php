<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http;

use Wepesi\Core\Application;
use Wepesi\Core\Http\Providers\BaseControllerMiddleware;
use Wepesi\Core\Media;
use Wepesi\Core\MetaData;
use Wepesi\Core\View\Provider\Contract\ViewEngineContracts;
use Wepesi\Core\View\View;

/**
 *
 */
abstract class Controller extends BaseControllerMiddleware
{
    /**
     * @var View
     */
    protected ViewEngineContracts $view;
    /**
     * @var MetaData
     */
    private MetaData $metadata;

    /**
     * Framework service initializer.
     *
     * This method is called automatically by the DI container after the controller
     * is created, so child controllers do not need to call parent::__construct().
     *
     * @return void
     */
    final public function __wepesiInit(): void
    {
        $this->view = Application::make(ViewEngineContracts::class);
        $this->metadata = Application::make(MetaData::class);
    }
}
