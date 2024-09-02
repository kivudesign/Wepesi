<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Rules;

use Wepesi\Core\Validation\Providers\RulesProvider;

/**
 * Array schema validation
 */
final class ArrayRules extends RulesProvider
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
     * @return $this|false
     */
    public function structure(array $elements): ?ArrayRules
    {
        if (isset($this->schema[$this->class_name]['string']) || isset($this->schema[$this->class_name]['number'])) {
            return false;
        }
        $this->schema[$this->class_name]['structure'] = $elements;
        return $this;
    }

    /**
     *  check if array content are(is) string
     * @return $this|false
     */
    public function string(): ?ArrayRules
    {
        if (isset($this->schema[$this->class_name]['number'])) {
            return false;
        }
        $this->schema[$this->class_name]['string'] = true;
        return $this;
    }

    /**
     * @return $this|false
     */
    public function number(): ?ArrayRules
    {
        if (isset($this->schema[$this->class_name]['string'])) {
            return false;
        }
        $this->schema[$this->class_name]['number'] = true;
        return $this;
    }
}