<?php

namespace Wepesi\Core\Database\WhereQueryBuilder;

use Wepesi\Core\Database\Providers\Contracts\WhereConditionContracts;

/**
 * @package Wepesi\Core\Database
 * @template WhereConditions of WhereConditionContracts
 * @template-implements  WhereConditionContracts<WhereConditions>
 */
class WhereConditions implements WhereConditionContracts
{
    /**
     * @var object
     */
    private object $field_condition;

    /**
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field_condition = (object)[
            'field_name' => $field,
            'comparison' => null,
            'field_value' => null,
            'operator' => ' AND '
        ];
    }

    /**
     * @param int|string $field_comparison
     * @return $this
     */
    public function isGreaterThan(int|string $field_comparison): WhereConditionContracts
    {
        return $this->setCondition('>', $field_comparison);
    }

    /**
     * @param string $comparison_sign
     * @param int|string $value
     * @return WhereConditionContracts
     */
    private function setCondition(string $comparison_sign, int|string $value): WhereConditionContracts
    {
        $this->field_condition->comparison = $comparison_sign;
        $this->validateStringOrNumeric($value);
        return $this;
    }

    /**
     * @param $field_value
     * @return void
     */
    private function validateStringOrNumeric($field_value): void
    {
        $this->field_condition->field_value = is_numeric($field_value) ? $field_value : (string)$field_value;
    }

    /**
     * @param int|string $field_comparison
     * @return $this
     */
    public function isGreaterEqualThan(int|string $field_comparison): WhereConditionContracts
    {
        return $this->setCondition('>=', $field_comparison);
    }

    /**
     * @param int|string $field_comparison
     * @return $this
     */
    public function isLessThan(int|string $field_comparison): WhereConditionContracts
    {
        return $this->setCondition('<', $field_comparison);
    }

    /**
     * @param int|string $field_comparison
     * @return $this
     */
    public function isLessEqualThan(int|string $field_comparison): WhereConditionContracts
    {
        return $this->setCondition('<=', $field_comparison);
    }

    /**
     * @param int|string $field_comparison
     * @return $this
     */
    public function isEqualTo(int|string $field_comparison): WhereConditionContracts
    {
        return $this->setCondition('=', $field_comparison);
    }

    /**
     * @param int|string $field_comparison
     * @return $this
     */
    public function isDifferentTo(int|string $field_comparison): WhereConditionContracts
    {
        return $this->setCondition('<>', $field_comparison);
    }

    /**
     * @param int|string $field_comparison
     * @return $this
     */
    public function isNotEqualTo(int|string $field_comparison): WhereConditionContracts
    {
        return $this->setCondition('!=', $field_comparison);
    }

    /**
     * @param int|string $field_comparison
     * @return $this
     */
    public function isLike(int|string $field_comparison): WhereConditionContracts
    {
        return $this->setCondition('like', $field_comparison);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|void
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }
    }

    /**
     * @return object
     */
    private function getCondition(): object
    {
        return $this->field_condition;
    }
}
