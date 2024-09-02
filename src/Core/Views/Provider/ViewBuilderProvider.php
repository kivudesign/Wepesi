<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Views\Provider;

use Wepesi\Core\Escape;

class ViewBuilderProvider implements Contract\ViewsContract
{
    protected array $data = [];

    /**
     * @var string
     */
    protected string $folder_name = '';

    /**
     * @param string $folder_name
     * @return void
     */
    public function setFolder(string $folder_name)
    {
        $this->folder_name = Escape::addSlashes($folder_name);
    }

    /**
     * @inheritDoc
     */
    public function display(string $view)
    {
        // TODO: Implement display() method.
    }

    /**
     * assign variables data to be displayed on file_page
     *
     * @param string $variable
     * @param        $value
     */
    public function assign(string $variable, $value)
    {
        $this->data[$variable] = $value;
    }

    /**
     * List all data assigned before being displayed
     * @return array
     */
    public function getAssignData(): array
    {
        return $this->data;
    }
}