<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Views;

use DOMDocument;
use DOMXPath;
use Exception;
use Wepesi\Core\Application;
use Wepesi\Core\Escape;
use Wepesi\Core\Http\Response;
use Wepesi\Core\MetaData;
use Wepesi\Core\Views\Provider\ViewBuilderProvider;
use function libxml_clear_errors;
use function libxml_use_internal_errors;

/**
 * @template T
 * @template-extends ViewBuilderProvider<T>
 */
class View extends ViewBuilderProvider
{
    /**
     * @var T[]
     */
    private static array $js_link_path = [];
    /**
     * @var array
     */
    private static array $style_link_path = [];
    /**
     * @var string|null
     */
    private static ?string $metadata = null;


    /**
     * Provide js link to be set up on the page header
     * @param class-string<T> $path_file
     * @return void
     */
    public static function setJsToHead(string $path_file): void
    {
        self::$js_link_path[] = $path_file;
    }

    /**
     * Provide CSS link path to set up stylesheet on the page header
     *
     * @param string $path
     * @return void
     *
     */
    public static function setStyleToHead(string $path): void
    {
        self::$style_link_path[] = $path;
    }

    /**
     * @param MetaData $metadata
     *
     * @return void
     */
    public static function setMetaData(MetaData $metadata): void
    {
        self::$metadata = $metadata->build();
    }

    /**
     * call this method to display file content
     *
     * @param class-string<T> $view
     */
    public function display(string $view): void
    {
        $view_file = $this->buildFilePath($view);
        $render = $this->renderView($view_file);
        if ($this->layout === '' && !$this->reset) {
            $this->layout = Application::getLayout();
        }
        if ($this->layout !== '') {
            $render = $this->renderLayout($render);
        }
        $this->buildAssetHead($render);
    }

    /**
     * @param class-string<T> $file_name
     *
     * @return string|null
     */
    private function buildFilePath(string $file_name): ?string
    {
        $folder = strlen(trim($this->folder_name)) > 0 ? $this->folder_name : Application::getViewFolder();;
        $view_file = Escape::checkFileExtension($file_name);
        $file_source = $folder . Escape::addSlashes($view_file);
        return Application::$ROOT_DIR . '/views' . $file_source;
    }

    /**
     * Render content from a provided file
     * @param class-string<T> $view
     *
     * @return false|string|void
     */
    protected function renderView(string $view)
    {
        if ($view && is_file($view)) {
            foreach ($this->data as $key => $value) {
                $$key = $value;
            }
            ob_start();
            include_once $view;
            return ob_get_clean();
        } else {
            $this->renderNotDefined($view);
        }
    }

    /**
     * @param class-string<T> $file_name
     *
     * @return void
     */
    private function renderNotDefined(string $file_name): void
    {
        Response::send(['exception' => "$file_name file path is not correct."],404);
    }

    /**
     * @param class-string<T> $view
     *
     * @return false|string|void
     */
    protected function renderLayout(string $view)
    {
        if ($this->layout_content === '') {
            $this->layout_content = Application::getLayoutContent();
        }
        if ($this->layout && is_file($this->layout)) {
            $layout_data = $this->data;
            // set the layout content variable to be used on the layout template
            $layout_data[$this->layout_content] = $view;
            // build the layout variables
            foreach ($layout_data as $key => $value) {
                $$key = $value;
            }
            ob_start();
            include_once $this->layout;
            return ob_get_clean();
        } else {
            $this->renderNotDefined($this->layout);
        }
    }


    /**
     *   this module will help to create a dom document to add
     *   asset js | CSS on the head of your file.
     * @param $html
     *
     * @return void
    */
    private function buildAssetHead($html): void
    {
        try {
            if (!$html) {
                throw new Exception('Unable to render empty file');
            }
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML(
                mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
            );
//            $errors = libxml_get_errors();
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);
            $head = $xpath->query('//head/title');
            $template = $dom->createDocumentFragment();
            // add a style link to the head tag of the page
            foreach (self::$style_link_path as $key => $value) {
                $src = $this->generateStyleLink($value);
                $template->appendXML($src);
                $head[0]->parentNode->insertbefore($template, $head[0]->nextSibling);
            }
            // add a script link to the head of the page
            foreach (self::$js_link_path as $key => $value) {
                $src = $this->generateStyleLink($value['link']);
                $template->appendXML($src);
                $head[0]->parentNode->insertbefore($template, $head[0]->nextSibling);
            }
            // add metadata to the head of the page
            if (self::$metadata) {
                $template->appendXML(self::$metadata);
                if ($head[0]) {
                    $head[0]->parentNode->insertbefore($template, $head[0]->nextSibling);
                }
            }
            print(html_entity_decode($dom->saveHTML()));
        } catch (Exception $ex) {
            print_r(['exception' => $ex->getMessage()]);
        }
    }

    /**
     * @param class-string<T> $path
     * @return T
     */
    private function generateJSLink(string $path): string
    {
        return '<script src="' . $path . '" type="text/javascript"></script>';
    }

    /**
     * @param class-string<T> $path
     * @return T
     */
    private function generateStyleLink(string $path): string {
        return '<link rel="stylesheet" type="text/css" href="' . $path . '">';
    }

}
