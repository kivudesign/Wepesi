<?php

namespace Wepesi\Core\Http;

use Wepesi\Core\Escape;
use Wepesi\Core\Validation\Rules;
use Wepesi\Core\Validation\Validate;

/**
 *
 */
class Redirect
{
    /**
     * @param $location
     * @return void
     */
    static function to($location = null)
    {
        $rule = new Rules();
        $validate = new Validate();
        $schema = ['link' => $rule->string()->url()];
        $source = ['link' => $location];
        if ($location) {
            // check if the location is an url
            $validate->check($source, $schema);
            if ($validate->passed()) {
                // Redirect a url
                header('Location:' . $location, true, 301);
                exit();
            } else {
                $webroot = substr(WEB_ROOT, 0, -1);
                $link = Escape::addSlashes($location);
                $location = $link == '' ? WEB_ROOT : $webroot . $link;
                header('Location:' . $location);
                exit();
            }
        } else {
            //TODO Design a 404 pages in case file or page does not exist
            header('HTTP/1.0 404 Not Found', true, 404);
            die();
        }
    }
}
