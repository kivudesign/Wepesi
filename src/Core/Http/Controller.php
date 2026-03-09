<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http;

use Wepesi\Core\Http\Providers\BaseControllerMiddleware;
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
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->view = new View();
    }
}
