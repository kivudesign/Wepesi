<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database\Traits;

use Exception;
use ReflectionClass;
use ReflectionProperty;
use Wepesi\Core\Database\Providers\Contracts\BaseRelationInterface;
use Wepesi\Core\Database\Providers\Contracts\EntityContracts;

/**
 *
 */
trait EntityReflexionTrait
{
    /**
     * @param $entity
     * @return array|object
     */
    protected function extractEntityDefinition($entity)
    {
        return $this->getClassEntityDefinition($entity);
    }
    /**
     * @param $entity
     * @return array|object
     */
    protected function getEntityRelation($entity)
    {
        return $this->getClassEntityDefinition($entity, true);
    }

    /**
     * @param $entity
     * @return array|object
     */
    protected function getEntityName($entity)
    {
        return $this->getClassEntityDefinition($entity, false, true);
    }

    /**
     * Get entity information's it can be for table information's related to the table
     * @param $entity EntityContracts|BaseRelationInterface
     * @param bool $entity_relation default false, use true in case we want to get more information about entity relation
     * @param bool $get_only_entity_name default false, use True in cas ww want to get only entity name or object.
     * @return array|object
     */
    private function getClassEntityDefinition($entity, bool $entity_relation = false, bool $get_only_entity_name = false)
    {
        try {
            $reflexion = new ReflectionClass($entity);
            $classEntityName = $reflexion->getShortName();
            $entity_prop ['class'] = $classEntityName;

            $table_name = strtolower($classEntityName);
            $entity_prop ['table'] = $table_name;
            //
            $table_field = [];
            // get entity table field defined as public properties
            foreach ($reflexion->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $table_field[] = $property->getName();
            }
            $entity_prop ['fields'] = $table_field;
            $entity_prop ['entity_object'] = null;
            // if we are dealing with entity relation
            if ($entity_relation) {
                $class_name = lcfirst($classEntityName);
                // get table name
                $object_instance = $reflexion->newInstance();
                $get_table_name = $reflexion->getMethod('getTableName');
                // set methode public to be accessible
                if (isset($get_table_name)) $get_table_name->setAccessible(true);
                $table_name = $get_table_name->invoke($object_instance);
                //
                $parent_entity_reflexion = new ReflectionClass($this);
                $object = $parent_entity_reflexion->newInstance();
                $entity_prop ['table'] = $table_name;
                if ($parent_entity_reflexion->hasMethod($table_name)) {
                    $table_name_method = $parent_entity_reflexion->getMethod($table_name);
                    // set method public to be accessible
                    if (isset($table_name_method)) $table_name_method->setAccessible(true);
                    $entity_prop ['entity_object'] = $table_name_method->invoke($object);
                } else if ($parent_entity_reflexion->hasMethod(($class_name))) {
                    $table_name_method = $parent_entity_reflexion->getMethod($class_name);
                    // set method public to be accessible
                    if (isset($table_name_method)) $table_name_method->setAccessible(true);
                    $entity_prop ['entity_object'] = $table_name_method->invoke($object);
                } else {
                    throw new Exception('You should implement a relation method for the ' . $class_name . ' from class ' . $classEntityName);
                }
            } else if ($get_only_entity_name) {
                // get table name
                $object_instance = $reflexion->newInstance();
                $get_table_name = $reflexion->getMethod('getTableName');
                // set method public to be accessible
                if (isset($get_table_name)) $get_table_name->setAccessible(true);
                $entity_prop ['table'] = $get_table_name->invoke($object_instance);
            }
            return (object)$entity_prop;
        } catch (Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }
}
