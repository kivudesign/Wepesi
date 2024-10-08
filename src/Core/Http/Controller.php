<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http;

use Wepesi\Core\Http\Providers\BaseControllerMiddleware;
use Wepesi\Core\Views\Provider\Contract\ViewsContract;
use Wepesi\Core\Views\View;

/**
 *
 */
abstract class Controller extends BaseControllerMiddleware
{
    /**
     * @var View
     */
    protected ViewsContract $view;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->view = new View();
    }
}
