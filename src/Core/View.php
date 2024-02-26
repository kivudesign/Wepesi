<?php

namespace Wepesi\Core;

use \DOMDocument;
use \DOMXPath;
use Exception;
use Wepesi\Core\Http\Response;
use function libxml_clear_errors;
use function libxml_use_internal_errors;

/**
 *
 */
class View
{
    /**
     *
     */
    private bool $reset = false;
    /**
     * @var array
     */
    private static array $jslink = [];
    /**
     * @var array
     */
    private static array $stylelink = [];
    /**
     * @var string|null
     */
    private static ?string $metadata = null;
    /**
     * @var array
     */
    private array $data = [];
    /**
     * @var string
     */
    private string $folder_name = '';
    /**
     * @var string
     */
    private string $layout = '';
    /**
     * @var string|null
     */
    private string $layout_content = '';

    /**
     *
     */
    public function __construct()
    {
        $this->folder_name = Application::getViewFolder();
    }

    /**
     * @param string $js_link
     *
     * @return void
     */
    public static function setJsToHead(string $js_link, bool $external = false)
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
     * @param MetaData $metadata
     *
     * @return void
     */
    public static function setMetaData(MetaData $metadata)
    {
        self::$metadata = $metadata->build();
    }

    /**
     * @param string $folder_name
     * @return void
     */
    public function setFolder(string $folder_name)
    {
        $this->folder_name = Escape::addSlaches($folder_name);
    }

    /**
     * call this method to display file content
     *
     * @param string $view
     */
    public function display(string $view)
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
        Response::send(['exception' => "$file_name file path is not correct."],404);
    }

    /**
     * @param string $view
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
     * @param $html
     *
     * @return void
     *  this module will help to create a dom document to add
     *  asset js | css on the head of your file.
     */
    private function buildAssetHead($html)
    {
        try {
            if (!$html) {
                throw new Exception('Unable to render empty data');
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
            foreach (self::$stylelink as $k => $v) {
                $template->appendXML('<link rel="stylesheet" type="text/css" href="' . $v . '">');
                $head[0]->parentNode->insertbefore($template, $head[0]->nextSibling);
            }
            // add a script link to the head of the page
            foreach (self::$jslink as $k => $v) {
                $link = $v['link'];
                $src = '<script src="' . $link . '" type="text/javascript"></script>';
                if (!$v['external']) $src = Bundles::insertJS($link, false, true);
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
     * you should provide the extension of your file,
     * in another case the file will be missing
     * @param string $template
     *
     * @return void
     */
    public function setLayout(string $template)
    {
        $this->layout = Application::$ROOT_DIR . '/views' . $template;
    }

    /**
     * @param string $layout_name
     * @return void
     */
    public function setLayoutContent(string $layout_name)
    {
        $this->layout_content = $layout_name;
    }

    /**
     * @return void
     */
    public function resetConfig(){
        $this->reset = true;
        $this->layout = '';
        $this->folder_name = '';
    }
}