<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\View;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMXPath;
use Exception;
use Wepesi\Core\Application;
use Wepesi\Core\Escape;
use Wepesi\Core\Http\Response;
use Wepesi\Core\MetaData;
use Wepesi\Core\View\Provider\Contract\ViewEngineContracts;
use Wepesi\Core\View\Provider\ViewBuilderProviders;
use function libxml_clear_errors;
use function libxml_use_internal_errors;

/**
 * @package Wepesi\Core\View
 * @template T
 * @template-extends ViewBuilderProviders<T>
 * @template-implements ViewEngineContracts<T>
 */
class View extends ViewBuilderProviders implements ViewEngineContracts
{
    /**
     * @var T[]
     */
    private static array $js_path = [];
    /**
     * @var array
     */
    private static array $style_path = [];
    /**
     * @var string|null
     */
    private static ?string $metadata = null;

    /**
     * Add a js module link file path to the header of the page
     * @param string $file_path
     * @return void
     */
    public static function addModuleJsFile(string $file_path): void
    {
        self::$js_path['module'][] = $file_path;
    }

    /**
     * Add a CSS link file path to the header of the page
     *
     * @param string $path
     * @return void
     *
     */
    public static function addCssFile(string $path): void
    {
        self::$style_path[] = $path;
    }

    /**
     * @param MetaData $metadata
     *
     * @return void
     */
    public static function addMetaData(MetaData $metadata): void
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
        $this->view_render = $this->renderView($view_file);
        if (! $this->getLayout() && !$this->reset) {
            $this->layout = Application::getLayout();
        }
        if ($this->getLayout()) {
            $this->view_render = $this->renderLayout($this->view_render);
        }
        print($this->view_render);
    }

    /**
     * @return string|null
     */
    public function getLayout(): ?string
    {
        return strlen(trim($this->layout)) > 0 ? $this->layout : null;
    }
    /**
     * @param class-string<T> $file_name
     *
     * @return string|null
     */
    private function buildFilePath(string $file_name): ?string
    {
        $folder = strlen(trim($this->folder_name)) > 0 ? $this->folder_name : Application::getViewPath();
        $view_file = Escape::checkFileExtension($file_name);
        $file_source = $folder . Escape::addSlashes($view_file);
        return $file_source;
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
            $this->layout_content = Application::getLayoutContentParam();
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
     * build the asset head of the page to be displayed.
     * @param ?string $html veb page content
     *
     * @return void
     * @throws DOMException
     */
    private function buildAssetHead(?string $html): void
    {
        try {
            if (!$html) {
                print('');
            }
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML(
                mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
            );
            $errors = libxml_get_errors();
            if ($errors) {
                throw $errors;
            }
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);
            $headNodes  = $xpath->query('//head');
            $head = null;
            if ($headNodes->length == 0) {
                // Create a new head element
                $head = $dom->createElement('head');
                // Optionally add a title or meta tag
                $title = $dom->createElement('title', 'App Title');
                $head->appendChild($title);
                // Append head to HTML
                $html = $dom->getElementsByTagName('html')->item(0);
                $html->insertBefore($head, $html->firstChild);
            } else {
                $head = $headNodes->item(0);
            }
            $documentFragment = $dom->createDocumentFragment();
            // add a style link to the head tag of the page
            foreach (self::$style_path as $value) {
                $link = $this->buildStyleLink($dom, $value);
                $head->appendChild($link);
            }
            // add a script link to the head of the page
            foreach (self::$js_path as $key => $array) {
                $type = $key != 'module' ? null : 'module';
                foreach ($array as $value) {
                    $link = $this->buildJSLink($dom, $value, $type);
                    $head->appendChild($link);
                }
            }
            // add metadata to the head of the page
            if (self::$metadata) {
                $documentFragment->appendXML(self::$metadata);
                if ($head[0]) {
                    $head[0]->parentNode->insertbefore($documentFragment, $head[0]->nextSibling);
                }
            }
            print(html_entity_decode($dom->saveHTML()));
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Use DomDocument to build javascript tag object
     *
     * @param DOMDocument $dom dom object
     * @param string $path JavaScript file path for script type
     * @return DOMElement
     * @throws DOMException
     */
    private function buildJSLink(DOMDocument $dom, string $path, string $type = 'text/javascript'): DOMElement
    {
        $link = $dom->createElement('script');
        $link->setAttribute('type', $type);
        $link->setAttribute('src', $path);
        return $link;
    }

    /**
     * Use DomDocument to build style lik for HTML output.
     *
     * @param DOMDocument $dom dom object
     * @param string $path css file path
     * @return DOMElement     *
     * @throws DOMException
     */
    private function buildStyleLink(DOMDocument $dom, string $path): DOMElement {
        $link = $dom->createElement('link');
        $link->setAttribute('rel', 'stylesheet');
        $link->setAttribute('type', 'text/css');
        $link->setAttribute('href', $path);
        return $link;
    }
}
