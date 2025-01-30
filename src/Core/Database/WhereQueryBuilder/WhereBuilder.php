<?php

namespace Wepesi\Core\Database\WhereQueryBuilder;

use Wepesi\Core\Database\Providers\Contracts\WhereBuilderContracts;
use Wepesi\Core\Database\Providers\Contracts\WhereConditionContracts;

/**
 * @package Wepesi\Core\Database
 * @template WhereBuilder of WhereBuilderContracts
 * @template-implements WhereBuilderContracts<WhereBuilder>
 */
final class WhereBuilder implements WhereBuilderContracts
{
    /**
     * @var array
     */
    private array $operator;

    /**
     *
     */
    public function __construct()
    {
        $this->operator = [];
    }

    /**
     * @param WhereConditionContracts $where_condition
     * @return $this
     */
    public function orOption(WhereConditionContracts $where_condition): WhereBuilderContracts
    {
        $condition = $where_condition->getCondition();
        $condition->operator = ' OR ';
        return $this->buildConditionOperator($condition);
    }

    /**
     * @param WhereConditionContracts $where_condition
     * @return $this
     */
    public function andOption(WhereConditionContracts $where_condition): WhereBuilderContracts
    {
        return $this->buildConditionOperator($where_condition->getCondition());
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
     * @param WhereBuilderContracts $where_builder
     * @return array[]
     */
    public function groupOption(WhereBuilderContracts $where_builder): array
    {
        // TODO implement group conditions.
        return [];
    }

    /**
     * @return array
     */
    protected function generate(): array
    {
        return $this->operator;
    }

    private function buildConditionOperator($buildCondition): WhereBuilderContracts {
        $this->operator[] = $buildCondition;
        return  $this;
    }
}
