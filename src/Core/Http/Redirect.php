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
     * @param string $location can be an url path a file path
     * @return void
     */
    static public function to(string $location = '')
    {
        $rule = new Rules();
        $validate = new Validate();
        $schema = ['link' => $rule->string()->url()];
        $source = ['link' => $location];
        if (strlen(trim($location)) != 0) {
            // check if the location is an url
            $validate->check($source, $schema);
            if ($validate->passed()) {
                // Redirect a url
                header('Location:' . $location, true, 301);
            } else {
                $webroot = substr(WEB_ROOT, 0, -1);
                $link = Escape::addSlashes($location);
                $location = $link == '' ? WEB_ROOT : $webroot . $link;
                header('Location:' . $location);
            }
        } else {
            header('HTTP/1.0 404 Not Found', true, 404);
        }
        exit();
    }
}
