<?php

namespace Wepesi\Core\Database\WhereQueryBuilder;

/**
 *
 */
final class WhereConditions
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
     * @param $field_comparison
     * @return $this
     */
    public function isGreaterThan($field_comparison): WhereConditions
    {
        $this->field_condition->comparison = '>';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_value
     * @return void
     */
    private function conditionIsString($field_value)
    {
        $this->field_condition->field_value = is_numeric($field_value) ? $field_value : "" . $field_value . "";
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isGreaterEqualThan($field_comparison): WhereConditions
    {
        $this->field_condition->comparison = '>=';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isLessThan($field_comparison): WhereConditions
    {
        $this->field_condition->comparison = '<';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isLessEqualThan($field_comparison): WhereConditions
    {
        $this->field_condition->comparison = '<=';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isEqualTo($field_comparison): WhereConditions
    {
        $this->field_condition->comparison = '=';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isDifferentTo($field_comparison): WhereConditions
    {
        $this->field_condition->comparison = '<>';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isNotEqualTo($field_comparison): WhereConditions
    {
        $this->field_condition->comparison = '!=';
        $this->conditionIsString($field_comparison);
        return $this;
    }

    /**
     * @param $field_comparison
     * @return $this
     */
    public function isLike($field_comparison): WhereConditions
    {
        $this->field_condition->comparison = 'like';
        $this->conditionIsString($field_comparison);
        return $this;
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
