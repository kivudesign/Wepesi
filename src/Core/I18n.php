<?php


namespace Wepesi\Core;

/**
 *
 */
class I18n
{
    /**
     * @var string
     */
    private string $lang;

    /**
     * @param string $lang
     */
    function __construct(string $lang = "en")
    {
        if (!Session::exists('lang')) {
            Session::put('lang', $lang);
        }
        $this->lang = Session::get('lang');
    }

    /**
     * This module help to translate a text from one language to another according to what has been defined
     * in case there is not corresponding value it will return the same text.
     * Note that you can also translate text with dynamic value with will be store on the data array.
     * eg: $text="hello %s",$data["world"], for more detail goto https://github.com/bim-g/wepesi_i18n
     * @param string $message :text to be translated
     * @param array $data : data to be injected on the translation text
     * @return string
     */
    function translate(string $message, array $data = []): string
    {
        $file = ROOT . "lang/" . $this->lang . "/language.php";
        if (!is_file($file) && !file_exists($file)) {
            $file = ROOT . "lang/en/language.php";
        }
        include($file);
        $message_key = !isset($language[$message]) ? $message : $language[$message];
        if (count($data) > 0) {
            $key_value = !isset($language[$message]) ? null : $language[$message];
            $message_key = $key_value != null ? vsprintf($key_value, $data) : vsprintf($message, $data);
        }
        return $message_key;
    }
    //TODO add methode that will help to analyse all content off each file and add missing key to the other files to have the same key in all the app.
}