<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Views\Provider;

use Wepesi\Core\Application;
use Wepesi\Core\Escape;
use Wepesi\Core\Views\Provider\Contract\ViewsContract;

/**
 * @template T
 * @template-implements ViewsContract<T>
 */
abstract class ViewBuilderProvider implements ViewsContract
{
    /**
     * @var T[]
     */
    protected array $data = [];
    /**
     * $reset
     * @var bool
     */
    protected bool $reset = false;

    /**
     * @var string
     */
    protected string $layout = '';
    /**
     * @var string
     */
    protected string $layout_content = '';
    /**
     * @var string
     */
    protected string $folder_name = '';

    /**
     * @param class-string<T> $folder_name
     *
     */
    public function setFolder(string $folder_name): void
    {
        $this->folder_name = Escape::addSlashes($folder_name);
    }

    /**
     * @inheritDoc
     */
    abstract public function display(string $view);
    /**
     * assign variables data to be displayed on file_page
     *
     * @param class-string<T> $variable
     * @param        $value
     */
    public function assign(string $variable, $value): void
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

    /**
     * render html string text
     * @param class-string<T> $html     *
     */
    public function renderHTML(string $html): void
    {
        print($html);
    }

    /**
     * you should provide the extension of your file,
     * in another case the file will be missing
     * @param class-string<T> $template
     */
    public function setLayout(string $template): void
    {
        $this->layout = Application::$ROOT_DIR . '/views' . $template;
    }

    /**
     * @param class-string<T> $layout_name
     *
     */
    public function setLayoutContent(string $layout_name): void
    {
        $this->layout_content = $layout_name;
    }

    /**
     * Reset view to default configuration     *
     */
    public function flush(): void
    {
        $this->reset = true;
        $this->layout = '';
        $this->folder_name = '';
    }
}