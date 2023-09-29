<?php

namespace Wepesi\Core\Orm\Relations;

use ReflectionClass;
use Wepesi\Core\Orm\EntityModel\EntityReflexionTrait;
use Wepesi\Core\Orm\EntityModel\Provider\Contract\EntityInterface;
use Wepesi\Core\Orm\Relations\Provider\Contract\BaseRelationInterface;

/**
 *
 */
abstract class BaseRelation implements BaseRelationInterface
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
    use EntityReflexionTrait;

    /**
     * @param EntityInterface $entity_parent
     * @param EntityInterface $entity_child
     */
    public function __construct(EntityInterface $entity_parent, EntityInterface $entity_child)
    {
        $this->parent_table = $this->getClassDefinition($entity_parent, false, true)->table;
        $this->child_table = $this->getClassDefinition($entity_child, false, true)->table;
        $this->table_relations = [];
    }

    /**
     * create link relation between to two entity.
     *
     * @param string $reference_key The parent entity relation id
     * @param string $foreignKey the child entity relation id
     * @return BaseRelation
     */
    public function linkOn(string $reference_key, string $foreignKey): BaseRelation
    {
        $this->table_relations = [
            'parent' => $this->parent_table,
            'child' => $this->child_table,
            'type' => (new ReflectionClass($this))->getShortName(),
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
     * Get relation type information about two entities
     *  parent the parent entity table,
     *  child the child entity table,
     *  type the entity relation type (hasOne, hasMany,...),
     *  primary_key entity primary key references not for table,
     *  foreign_key entity primary key references
     * @return object
     */
    protected function getRelation(): object
    {
        return (object)$this->table_relations;
    }
}