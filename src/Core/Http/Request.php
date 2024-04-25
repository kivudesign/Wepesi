<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http;

class Request
{
    public string $uri;
    public string $method;
    public array $get;
    public array $post;
    public array $files;
    public array $cookie;
    public array $server;

    public function __construct(string $uri,
                                string $method,
                                array  $get,
                                array  $post,
                                array  $files,
                                array  $cookie,
                                array  $server)
    {
        $this->server = $server;
        $this->cookie = $cookie;
        $this->files = $files;
        $this->post = $post;
        $this->get = $get;
        $this->method = $method;
        $this->uri = $uri;
    }

    public static function createFromGlobals(): Request
    {
        return new static($_SERVER['REQUEST_URI'],
            $_SERVER['REQUEST_METHOD'],
            $_GET,
            $_POST,
            $_FILES,
            $_COOKIE,
            $_SERVER);
    }
}
