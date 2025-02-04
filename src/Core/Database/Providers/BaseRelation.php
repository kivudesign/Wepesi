<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database\Providers;

use ReflectionClass;
use Wepesi\Core\Database\Providers\Contracts\BaseRelationInterface;
use Wepesi\Core\Database\Providers\Contracts\EntityContracts;
use Wepesi\Core\Database\Traits\EntityReflexionTrait;

/**
 * @package Wepesi\Core\Database
 * @template BaseRelation of BaseRelationInterface
 * @template-implements BaseRelationInterface<BaseRelation>
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
     * @param EntityContracts $entity_parent
     * @param EntityContracts $entity_child
     */
    public function __construct(EntityContracts $entity_parent, EntityContracts $entity_child)
    {
        $this->parent_table = $this->getEntityName($entity_parent)->table;
        $this->child_table = $this->getEntityName($entity_child)->table;
        $this->table_relations = [];
    }

    /**
     * create link relation between to two entities.
     *
     * @param string $reference_key The parent entity relation id
     * @param string $foreignKey the child entity relation id
     * @return BaseRelationInterface
     */
    public function linkOn(string $reference_key, string $foreignKey): BaseRelationInterface
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
     *  type the entity relation type (hasOne, hasMany, ...),
     *  primary_key entity primary key references not for table,
     *  foreign_key entity primary key references
     * @return object
     */
    protected function getRelation(): object
    {
        return (object)$this->table_relations;
    }
}
