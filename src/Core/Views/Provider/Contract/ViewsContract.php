<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Views\Provider\Contract;

/**
 * @template T
 */
interface ViewsContract
{
    /**
     * Setup new folder location for layout template
     * @var class-string<T> $folder_name
     */
    public function setFolder(string $folder_name): void;

    /**
     * render html content
     * @var class-string<T> $view
     */
    public function display(string $view);

    /**
     * assign variables data to be displayed on file_page
     * @var class-string<T> $variable
     * @var <T>  $value
     */
    public function assign(string $variable, $value);

    /**
     * List all data assigned before being displayed
     * @return array
     */
    public function getAssignData(): array;

    /**
     * Render html string text
     * @var class-string<T> $html
     */
    public function renderHTML(string $html): void;

    /**
     * @var class-string<T> $template
     */
    public function setLayout(string $template): void;

    /**
     * provide layout content name
     * @var class-string<T> $layout_name
     */
    public function setLayoutContent(string $layout_name): void;

    /**
     * reset view to default configuration
     */
    public function flush(): void;
}
