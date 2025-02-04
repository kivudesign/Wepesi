<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database\Traits;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Wepesi\Core\Database\Providers\Contracts\EntityContracts;
use Wepesi\Core\Exceptions\DatabaseException;

/**
 * @package Wepesi\Core\Database
 * @template EntityReflexionTrait
 */
trait EntityReflexionTrait
{
    /**
     * Extract an entity definition object
     * @param EntityContracts $entity
     * @return object
     */
    protected function extractEntityDefinition(EntityContracts $entity): object
    {
        return $this->getClassEntityDefinition($entity);
    }

    /**
     * Get entity information's it can be for table information's related to the table
     * @param EntityContracts $entity EntityContracts|BaseRelationInterface
     * @param bool $entity_relation set to true to get more information about entity relation
     * @param bool $get_entity_name_only set to True to get entity name only otherwise get a data object.
     * @return object
     * @throws ReflectionException|DatabaseException
     */
    private function getClassEntityDefinition(EntityContracts $entity, bool $entity_relation = false, bool $get_entity_name_only = false): object
    {
        $reflexion = new ReflectionClass($entity);
        $classEntityName = $reflexion->getShortName();
        $entity_prop['class'] = $classEntityName;

        $table_name = strtolower($classEntityName);
        $entity_prop['table'] = $table_name;
        //
        $table_field = [];
        // get entity table field defined as public properties
        foreach ($reflexion->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $table_field[] = $property->getName();
        }
        $entity_prop['fields'] = $table_field;
        $entity_prop['entity_object'] = null;
        // if we are dealing with entity relation
        if ($entity_relation) {
            $class_name = lcfirst($classEntityName);
            // get table name
            $object_instance = $reflexion->newInstance();
            $get_table_name = $reflexion->getMethod('getTableName');
            // set methode public to be accessible
//            if (isset($get_table_name)) $get_table_name->setAccessible(true);
            $table_name = $get_table_name->invoke($object_instance);
            //
            $parent_entity_reflexion = new ReflectionClass($this);
            $object = $parent_entity_reflexion->newInstance();
            $entity_prop['table'] = $table_name;
            if ($parent_entity_reflexion->hasMethod($table_name)) {
                $table_name_method = $parent_entity_reflexion->getMethod($table_name);
                // set method public to be accessible
//                if (isset($table_name_method)) $table_name_method->setAccessible(true);
                $entity_prop['entity_object'] = $table_name_method->invoke($object);
            } else if ($parent_entity_reflexion->hasMethod(($class_name))) {
                $table_name_method = $parent_entity_reflexion->getMethod($class_name);
                // set method public to be accessible
//                if (isset($table_name_method)) $table_name_method->setAccessible(true);
                $entity_prop['entity_object'] = $table_name_method->invoke($object);
            } else {
                throw new DatabaseException('You should implement a relation method for the ' . $class_name . ' from class ' . $classEntityName);
            }
        } else if ($get_entity_name_only) {
            // get table name
            $object_instance = $reflexion->newInstance();
            $get_table_name = $reflexion->getMethod('getTableName');
            // set method public to be accessible
//            if (isset($get_table_name)) $get_table_name->setAccessible(true);
            $entity_prop['table'] = $get_table_name->invoke($object_instance);
        }
        return (object)$entity_prop;
    }

    /**
     * Get an Entity data object
     * @param EntityContracts $entity
     * @return object
     */
    protected function getEntityRelation(EntityContracts $entity): object
    {
        return $this->getClassEntityDefinition($entity, true);
    }

    /**
     * Get entity object name
     * @param EntityContracts $entity
     * @return object
     */
    protected function getEntityName(EntityContracts $entity): object
    {
        return $this->getClassEntityDefinition($entity, false, true);
    }
}
