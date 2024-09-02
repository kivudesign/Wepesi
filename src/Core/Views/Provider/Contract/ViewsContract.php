<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Views\Provider\Contract;

use Wepesi\Core\MetaData;

/**
 *
 */
interface ViewsContract
{
    /**
     * Setup new folder location for layout template
     * @param string $folder_name
     * @return mixed
     */
    public function setFolder(string $folder_name);

    /**
     * render html content
     * @param string $view
     * @return mixed
     */
    public function display(string $view);

    /**
     * @param string $variable
     * @param mixed $value
     * @return mixed
     */
    public function assign(string $variable, $value);

    /**
     * @return array
     */
    public function getAssignData(): array;
}