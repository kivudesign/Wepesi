<?php
/*
 * Copyright (c) 2026. Wepesi Framework
 */

namespace Wepesi\Core\Component\Providers;

use Wepesi\Core\Application;
use Wepesi\Core\Component\Providers\Contracts\ComponentContracts;
use Wepesi\Core\Escape;

/**
 * @package Wepesi\Core\Component
 * @template BaseComponent of ComponentContracts
 * @template-implement ComponentContract<BaseComponent>
 */

abstract class BaseComponent implements ComponentContracts
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
    public function id(string $id): ComponentContracts
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
    public function class(string $class): ComponentContracts
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
    public function attribute(string $name, string $value): ComponentContracts
    {
        return $this->setAttribute($name,htmlspecialchars($value));
    }

    /**
     * element HTML data attribute
     *
     * @param string $name
     * @param string $value
     * @return ComponentContracts
     */
    public function data(string $name, string $value): ComponentContracts
    {
        return $this->setAttribute($name,htmlspecialchars($value));
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
     * @return ComponentContracts
     */
    public function loadViewFile(string $viewComponent, array $data = []): ComponentContracts
    {
        $componentsDirectory = Application::getViewPath() . '/components';
        $baseDirectory = realpath($componentsDirectory);
        if ($baseDirectory === false) {
            return $this;
        }
        $path = '/' . ltrim($viewComponent, '/');
        $path = Escape::checkFileExtension($path);
        $file = $componentsDirectory . $path;

        if (file_exists($file)) {
            $resolvedFile = realpath($file);
            $basePrefix = rtrim($baseDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            if ($resolvedFile !== false && ($resolvedFile === $baseDirectory || str_starts_with($resolvedFile, $basePrefix))) {
                // in case we need to patch data on the subcomponent we can split it like
                if (count($data) > 0) {
                    extract($data, EXTR_SKIP);
                }
                ob_start();
                require $file;
                $this->subComponents = ob_get_clean();
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return ComponentContracts
     */
    private function setAttribute(string $name, string $value): ComponentContracts
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
