<?php
/*
 * Copyright (c) 2023.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation;

/**
 *
 */
final class MessageErrorGenerator
{

    /**
     * @var array
     */
    private array $items;

    /**
     *
     */
    public function __construct(){
        $this->items = [];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function type(string $value): MessageErrorGenerator
    {
        $this->items['type'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function message(string $value): MessageErrorGenerator
    {
        $this->items['message'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function label(string $value): MessageErrorGenerator
    {
        $this->items['label'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function limit(string $value): MessageErrorGenerator
    {
        $this->items['limit'] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function generate(){
        return $this->items;
    }
}