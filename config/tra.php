<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */
/**
 * needs a description
 * @param string $content
 * @return mixed|string
 */function tr($content)
{
    $args = func_get_args();
    return tra($content, '', false, array_slice($args, 1));
}

/**
 * translate your text
 * @param string $message
 * @param string|array    $value
 * @return string
 */
function tra(string $message, $value = null): string
{
    $i18n = new \Wepesi\Core\i18n(\Wepesi\Core\Session::get('lang'));
    $translate_value = !is_array($value) ? [$value] : $value;
    return $i18n->translate($message, $translate_value);
}

function tra_js(){}