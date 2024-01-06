<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

/**
 *
 */
abstract class Controller
{
    /**
     * @var View
     */
    protected View $view;

    /**
     *
     */
    public function __construct(){
        $this->view = new View();
    }
}
