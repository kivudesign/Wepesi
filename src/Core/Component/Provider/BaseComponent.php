<?php
/*
 * Copyright (c) 2026. Wepesi Framework
 */

namespace Wepesi\Core\Component\Provider;

use Wepesi\Core\Application;
use Wepesi\Core\Component\Contracts\ComponentContract;
use Wepesi\Core\Escape;

/**
 * @package Wepesi\Core\Component
 * @template BaseComponent of ComponentContract
 * @template-implement ComponentContract<BaseComponent>
 */

abstract class BaseComponent implements ComponentContract
{
    /**
     * @var string
     */
    protected string $id = '';
    /**
     * @var string
     */
    protected string $class = '';
    /**
     * @var array
     */
    protected array $attributes = [];
    /**
     * @var array
     */
    protected array $dataAttribute = [];
    /**
     * @var string|false
     */
    protected string|false $subComponents = false;

    /**
     * element HTML id
     *
     * @param  mixed $id
     * @return self
     */
    public function id(string $id): ComponentContract
    {
        $this->id = htmlspecialchars($id);
        return $this;
    }
    /**
     * element HTML class
     *
     * @param  mixed $class
     * @return self
     */
    public function class(string $class): ComponentContract
    {
        $this->class = htmlspecialchars($class);
        return $this;
    }

    /**
     * element HTML attribute
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function attribute(string $name, string $value): ComponentContract
    {
        return $this->setAttribute($name,htmlspecialchars($value));;
    }

    /**
     * element HTML data attribute
     *
     * @param string $name
     * @param string $value
     * @return ComponentContract
     */
    public function data(string $name, string $value): ComponentContract
    {
        return $this->setAttribute($name,htmlspecialchars($value));;
    }

    /**
     * buildAttributes
     *
     * @return string
     */
    public function buildAttributes(): string
    {
        $attributes = '';
        if ($this->id) {
            $attributes .= ' id="' . $this->id . '"';
        }
        if ($this->class) {
            $attributes .= ' class="' . $this->class . '"';
        }
        foreach ($this->attributes as $name => $value) {
            $attributes .= ' ' . $name . '="' . $value . '"';
        }
        foreach ($this->dataAttribute as $name => $value) {
            $attributes .= ' data-' . $name . '="' . $value . '"';
        }
        return $attributes;
    }

    /**
     * @param string $viewComponent
     * @param array $data
     * @return ComponentContract
     */
    public function loadViewFile(string $viewComponent, array $data = []): ComponentContract
    {
        $path = '/' . ltrim($viewComponent, '/');
        $path = Escape::checkFileExtension($path);
        $file = Application::getRootDir() . '/views/components' . $path;
        if (file_exists($file)) {
            // in case we need to patch data on the subcomponent we can split it like
            if (count($data) > 0) {
                extract($data, EXTR_SKIP);
            }
            ob_start();
            require_once $file;
            $this->subComponents = ob_get_clean();
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return ComponentContract
     */
    private function setAttribute(string $name, string $value): ComponentContract
    {
        $this->dataAttribute[$name] = $value;
        return $this;
    }
    /**
     * render
     *
     * @param  mixed $data
     * @return string
     */
    abstract function render(array $data): string;
}
