<?php
/*
 * Copyright (c) 2023.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation;

/**
 *
 */
final class MessageErrorBuilder
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
    public function type(string $value): MessageErrorBuilder
    {
        $this->items['type'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function message(string $value): MessageErrorBuilder
    {
        $this->items['message'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function label(string $value): MessageErrorBuilder
    {
        $this->items['label'] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function limit(string $value): MessageErrorBuilder
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