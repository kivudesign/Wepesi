<?php

namespace Wepesi\Core\Orm\Relations;

use Wepesi\Core\Orm\EntityModel\EntityReflexion;
use Wepesi\Core\Orm\EntityModel\Provider\Contract\EntityInterface;
/**
 *
 */
abstract class BaseRelation
{
    /**
     * @var string|mixed
     */
    protected string $parent_table;
    /**
     * @var string|mixed
     */
    protected string $child_table;
    /**
     * @var array
     */
    protected array $table_relations;
    use EntityReflexion;

    /**
     * @param EntityInterface $entity_parent
     * @param EntityInterface $entity_child
     */
    public function __construct(EntityInterface $entity_parent, EntityInterface $entity_child)
    {
        $this->parent_table = $this->getClassDefinition($entity_parent,false,true)->table;
        $this->child_table = $this->getClassDefinition($entity_child,false,true)->table;
        $this->table_relations = [];
    }
    /**
     * @param string $reference_key
     * @param string $foreignKey
     * @return BaseRelation
     */
    public function linkOn(string $reference_key, string $foreignKey): BaseRelation
    {
        $this->table_relations = [
            'parent' => $this->parent_table,
            'child' => $this->child_table,
            'type' => $this->getClassDefinition($this)->class,
            'primary_key' => $reference_key,
            'foreign_key' => $foreignKey
        ];
        return $this;
    }

    /**
     * @param $method
     * @param $params
     * @return mixed|void
     */
    public function __call($method, $params)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $params);
        }
    }

    /**
     * @return object
     */
    protected function getRelation(): object
    {
        return (object)$this->table_relations;
    }
}