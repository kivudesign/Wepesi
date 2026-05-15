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
        return $this->setParams('lang', $lang);
    }

    /**
     * Set up application view location files
     * @param string $value
     * @return AppConfiguration
     */
    public function view(string $value = '/app/Views'): AppConfiguration {
        return $this->setParams('view', $value);
    }

    /**
     * Set up application routes location files
     * @param string $value
     * @return AppConfiguration
     */
    public function route(string $value = '/app/Routes'): AppConfiguration {
        return $this->setParams('route', $value);
    }
    /**
     * @param string $timezone
     * @return AppConfiguration
     */
    public function timezone(string $timezone = 'Africa/Kigali'): AppConfiguration
    {
        return $this->setParams('timezone', $timezone);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setNotFound(string $path = '/views/404.php'): AppConfiguration
    {
        return $this->setParams('not_found', $path);
    }

    /**
     * @param string $key
     * @param $value
     * @return AppConfiguration
     */
    private function setParams(string $key, $value): AppConfiguration {
        $this->params[$key] = $value;
        return $this;
    }
    /**
     * @return array
     */
    public function getCongigurations(): array
    {
        return $this->params;
    }
}