<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

class AppConfiguration
{
    private array $params;

    /**
     *  constructor.
     */
    public function __construct()
    {
        $this->params = [];
    }

    /**
     * @param string $lang
     * @return AppConfiguration
     */
    public function lang(string $lang = 'fr'): AppConfiguration
    {
        $this->params['lang'] = $lang;
        return $this;
    }

    /**
     * @param string $timezone
     * @return AppConfiguration
     */
    public function timezone(string $timezone = 'Africa/Kigali'): AppConfiguration
    {
        $this->params['timezone'] = $timezone;
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setNotFound(string $path = '404.php'): AppConfiguration
    {
        $this->params['not_found'] = $path;
        return $this;
    }

    /**
     * @return array
     */
    public function generate(): array
    {
        return $this->params;
    }
}