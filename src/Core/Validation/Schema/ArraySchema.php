<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Schema;

use Wepesi\Core\Validation\Providers\SChemaProvider;

/**
 * Arraye schema validation
 */
final class ArraySchema extends SChemaProvider
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }

    /**
     * @param array $elements data array to be validated
     * @return $this
     */
    public function structure(array $elements): ?ArraySchema
    {
        if (isset($this->schema[$this->class_name]['string']) || isset($this->schema[$this->class_name]['number'])) {
            return false;
        }
        $this->schema[$this->class_name]['structure'] = $elements;
        return $this;
    }

    /**
     *  check if array content are(is) string
     * @return $this|null
     */
    public function string(): ?ArraySchema
    {
        if (isset($this->schema[$this->class_name]['number'])) {
            return false;
        }
        $this->schema[$this->class_name]['string'] = true;
        return $this;
    }

    /**
     * @return $this|null
     */
    public function number(): ?ArraySchema
    {
        if (isset($this->schema[$this->class_name]['string'])) {
            return false;
        }
        $this->schema[$this->class_name]['number'] = true;
        return $this;
    }
}