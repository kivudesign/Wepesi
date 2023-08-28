<?php

namespace Wepesi\Core\Orm\Relations;

use Wepesi\Core\BaseEntityModel\Entity;
use Wepesi\Core\BaseEntityModel\EntityReflexion;

/**
 *
 */
class HasMany
{
    /**
     * @var string|mixed
     */
    private string $parent_table;
    /**
     * @var string|mixed
     */
    private string $child_table;
    /**
     * @var array
     */
    private array $table_relations;
    use EntityReflexion;

    /**
     * @param Entity $entity_parent
     * @param Entity $entity_child
     */
    public function __construct(Entity $entity_parent, Entity $entity_child)
    {
        $this->parent_table = $this->getEntityName($entity_parent)['table'];
        $this->child_table = $this->getEntityName($entity_child)['table'];
        $this->table_relations = [];
    }

    /**
     * @param string $reference_key
     * @param string $foreignKey
     * @return HasMany
     */
    public function linkOn(string $reference_key, string $foreignKey): HasMany
    {
        $this->table_relations = [
            'parent' => $this->parent_table,
            'child' => $this->child_table,
            'type' => 'hasMany',
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
    private function getRelation(): object
    {
        return (object)$this->table_relations;
    }
}