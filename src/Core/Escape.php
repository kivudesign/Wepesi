<?php

namespace Wepesi\Core;

class Escape{

    static function encode(string $input){
        $text = $input;
        if ($input != strip_tags($input)) {
            $text = htmlentities($input, ENT_QUOTES, 'UTF-8');
        }
        return $text;
    }
    static function decode(string $input){
        return html_entity_decode($input, ENT_QUOTES, 'UTF-8');
    }
}