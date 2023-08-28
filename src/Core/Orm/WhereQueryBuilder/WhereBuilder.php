<?php

namespace Wepesi\Core\Orm\WhereQueryBuilder;

/**
 *
 */
final class WhereBuilder
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
     * @param WhereConditions $where_condition
     * @return $this
     */
    public function orOption(WhereConditions $where_condition): WhereBuilder
    {
        $condition = $where_condition->getCondition();
        $condition->operator = ' OR ';
        $this->operator[] = $condition;
        return $this;
    }

    /**
     * @param WhereConditions $where_condition
     * @return $this
     */
    public function andOption(WhereConditions $where_condition): WhereBuilder
    {
        $this->operator[] = $where_condition->getCondition();;
        return $this;
    }

    /**
     * @param WhereBuilder $builder
     * @return array[]
     */
    protected function groupOption(WhereBuilder $builder): array
    {
        // TODO implement group conditions.
        // return ['groupe' => $builder->generate()];
        return [];
    }

    /**
     * @return array
     */
    protected function generate(): array
    {
        return $this->operator;
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
}
