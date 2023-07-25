<?php
/**
 * Doctawetu Application
 */

namespace Wepesi\Core\Resolver;

use Closure;

final class Option
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * @var bool
     */
    private bool $hasDefaultValue;

    /**
     * @var Closure|null
     */
    private ?Closure $validator;

    /**
     * Option constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->hasDefaultValue = false;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     * @return Option
     */
    public function setDefaultValue($defaultValue): self
    {
        $this->hasDefaultValue = true;
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    public function validator(Closure $closure): self
    {
        $this->validator = $closure;
        return $this;
    }

    public function isValid($value): bool
    {
        if ($this->validator instanceof Closure) {
            $validator = $this->validator;
            return $validator($value);
        }
        return true;
    }
}