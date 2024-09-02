<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Providers;

use Wepesi\Core\Validation\Providers\Contracts\RulesValidationContracts;

/**
 *
 */
abstract class RulesProvider implements RulesValidationContracts
{
    /**
     * @var array
     */
    protected array $schema = [];
    /**
     * @var string
     */
    protected string $class_name;

    /**
     * @param string $class_name
     */
    public function __construct(string $class_name)
    {
        $this->class_name = $class_name;
        $this->schema[$this->class_name] = [];
    }

    /**
     * @param int $rule
     * @return RulesProvider
     */
    public function min(int $rule): RulesProvider
    {
        $this->schema[$this->class_name]["min"] = $rule;
        return $this;
    }

    /**
     * @param $rule
     * @return $this
     */
    public function max($rule): RulesProvider
    {
        $this->schema[$this->class_name]["max"] = $rule;
        return $this;
    }

    /**
     * @return $this
     */
    public function required(): RulesProvider
    {
        $this->schema[$this->class_name]["required"] = true;
        return $this;
    }

    /**
     * @return array
     */
    public function generate(): array
    {
        return $this->schema;
    }
}