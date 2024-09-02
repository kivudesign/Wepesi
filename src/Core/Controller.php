<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Views\Provider\Contract\ViewsContract;
use Wepesi\Core\Views\View;

/**
 *
 */
abstract class Controller
{
    /**
     * @var View
     */
    protected ViewsContract $view;

    /**
     *
     */
    public function __construct(){
        $this->view = new View();
    }
}
