<?php

namespace Wepesi\Core\Orm\EntityModel;

use Exception;
use ReflectionClass;
use ReflectionProperty;
use Wepesi\Core\Orm\EntityModel\Provider\Contract\EntityInterface;
use Wepesi\Core\Orm\Relations\Provider\Contract\BaseRelationInterface;

/**
 *
 */
trait EntityReflexionTrait
{
    /**
     * @param $entity EntityInterface|BaseRelationInterface
     * @param bool $entity_relation default false, use true in case we want to get more information about entity relation
     * @param bool $get_only_entity_name default false, use True in cas ww want to get only entity name or object.
     * @return array|object
     */
    private function getClassDefinition($entity, bool $entity_relation = false, bool $get_only_entity_name = false)
    {
        try {
            $reflexion = new ReflectionClass($entity);
            $classEntityName = $reflexion->getShortName();
            $table_name = strtolower($classEntityName);
            $table_field = [];
            $entity_object = null;
            // get entity table field defined as public properties
            foreach ($reflexion->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $table_field[] = $property->getName();
            }

            /**
             * check in case we want to get more information about the entity relation.
             */
            if ($entity_relation) {
                $class_name = lcfirst($classEntityName);
                //get table name
                $entity_instance_object = $reflexion->newInstance();
                $getTableNameMethod = $reflexion->getMethod('getTableName');
                $getTableNameMethod->setAccessible(true);
                $table_name = $getTableNameMethod->invoke($entity_instance_object);
                //
                $parentEntityReflexion = new ReflectionClass($this);
                $parent_entity_instance_object = $parentEntityReflexion->newInstance();
                // check from parent entity if the child entity name as method as been defined.
                if ($parentEntityReflexion->hasMethod($table_name)) {
                    $method = $parent_entity_instance_object->getMethod($table_name);
                    $method->setAccessible(true);
                    $entity_object = $method->invoke($parent_entity_instance_object);
                } else if ($parentEntityReflexion->hasMethod($class_name)) {
                    $method = $parentEntityReflexion->getMethod($class_name);
                    $method->setAccessible(true);
                    $entity_object = $method->invoke($parent_entity_instance_object);
                } else {
                    throw new Exception('You should implement a relation method call ' . $class_name . ' from class ' . $classEntityName);
                }
            } elseif ($get_only_entity_name) {
                $instance_object = $reflexion->newInstance();
                $get_table_name_method = $reflexion->getMethod('getTableName');
                $get_table_name_method->setAccessible(true);
                $table_name = $get_table_name_method->invoke($instance_object);
            }
            return (object)[
                'table' => $table_name,
                'fields' => $table_field,
                'entity_object' => $entity_object,
                'class' => $classEntityName,
            ];
        } catch (Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }
}
