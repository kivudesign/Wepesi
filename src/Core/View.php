<?php

namespace Wepesi\Core;

use Wepesi\Core\Application;
use Wepesi\Core\MetaData;
use Wepesi\Core\Bundles;
use Wepesi\Core\Escape;
use Wepesi\Core\Response;

class View
{
    const ERROR_VIEW = '';
    private static array $jslink;
    private static array $stylelink;
    private static ?string $metadata;
    private array $data = [];
    private string $folder_name;
    private ?string $layout;
    private ?string $layout_content;

    /**
     * @param string|null $folder_name
     */
    function __construct(?string $folder_name = '/')
    {
        $this->folder_name = Escape::addSlaches($folder_name);
        $this->layout = Application::$LAYOUT;
        self::$jslink = [];
        self::$stylelink = [];
        self::$metadata = null;
        $this->layout_content = Application::$LAYOUT_CONTENT;
    }

    /**
     * @param string $folder_name
     * @return void
     */
    public function setFolder(string $folder_name = '/')
    {
        $this->folder_name = Escape::addSlaches($folder_name);
    }
    /**
     * @param string $js_link
     *
     * @return void
     */
    public static function setJsToHead(string $js_link,bool $external = false)
    {
        self::$jslink[] = [
            'link' => $js_link,
            'external' => $external
        ];
    }

    /**
     * @param string $style_link
     *
     * @return void
     * add
     */
    public static function setStyleToHead(string $style_link)
    {
        self::$stylelink[] = $style_link;
    }

    /**
     * @param BundleMetaData $metadata
     *
     * @return void
     */
    public static function setMetaData(BundleMetaData $metadata)
    {
        self::$metadata = $metadata->build();
    }

    /**
     * call this method to display file content
     *
     * @param string $view
     */
    function display(string $view)
    {
        $view_file = $this->buildFilePath($view);
        $render = $this->renderView($view_file);
        if ($this->layout) {
            $render = $this->renderLayout($render);
        }
        $this->buildAssetHead($render);
    }

    /**
     * @param string $file_name
     *
     * @return string|null
     */
    private function buildFilePath(string $file_name): ?string
    {
        $view_file = Escape::checkFileExtension($file_name);
        $file_source = $this->folder_name . Escape::addSlaches($view_file);
        return Application::$ROOT_DIR . '/views' . $file_source;
    }

    /**
     * @param string $view
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
     * @param string $file_name
     *
     * @return void
     */
    private function renderNotDefined(string $file_name)
    {
        Response::setStatusCode(404);
        print_r(['exception' => "$file_name file path is not correct."]);
    }

    /**
     * @param string $view
     *
     * @return false|string|void
     */
    protected function renderLayout(string $view)
    {
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
     * @param $html
     *
     * @return void
     *  this module will help to create a dom document to add
     *  asset js | css on the head of your file.
     */
    private function buildAssetHead($html)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(
            mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
        );
        //            $errors = libxml_get_errors();
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);
        $head = $xpath->query('//head/title');
        $template = $dom->createDocumentFragment();
        // add style link to the head tag of the page
        foreach (self::$stylelink as $k => $v) {
            $template->appendXML('<link rel="stylesheet" type="text/css" href="' . $v . '">');
            $head[0]->parentNode->insertbefore($template, $head[0]->nextSibling);
        }
        // add script link to the head of the page
        foreach (self::$jslink as $k => $v) {
            $link = $v['link'];
            $src = '<script src="' . $link . '" type="text/javascript"></script>';
            if(!$v['external']) $src = Bundles::insertJS($link,false,true);
            $template->appendXML($src);
            $head[0]->parentNode->insertbefore($template, $head[0]->nextSibling);
        }
        // add metadata to the head of the page
        if (self::$metadata) {
            $template->appendXML(self::$metadata);
            $head[0]->parentNode->insertbefore($template, $head[0]->nextSibling);
        }
        print(html_entity_decode($dom->saveHTML()));
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
     * @param string $template
     *
     * @return void
     */
    public function setLayout(string $template)
    {
        $this->layout = $template;
    }
}