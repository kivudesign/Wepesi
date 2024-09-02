<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Traits;

use Wepesi\Core\Exceptions\ValidationException;

/**
 *
 */
trait InitTrait
{
    /**
     * @param $source
     * @param $schema
     * @throws ValidationException
     */
    private function initInstance($source, $schema)
    {
        if (!is_array($source) || count($source) == 0) {
            throw new ValidationException('Your Source Data should not be en empty array', 500);
        }
        if (!is_array($schema) || count($schema) == 0) {
            throw new ValidationException('Your Schema should not be en empty array', 500);
        }
        $fields = array_keys($schema);

        if (!isset($source[$fields[0]])) {
            throw new ValidationException('field not defined',500);
        }

        $this->extract_data($schema);
    }

    /**
     * @param array $schema
     */
    protected function extract_data(array $schema): void
    {
        // TODO implement extract data
    }
}