<?php

namespace Wepesi\Core\Orm\EntityModel;

use Wepesi\Core\Orm\EntityModel\Provider\Contract\EntityInterface;

/**
 *
 */
trait EntityReflexion
{
    /**
     * @param EntityInterface $entity
     * @return object|array
     */
    private function getClassDefinition($entity, bool $entity_relation=false,bool $entity_name=false)
    {
        try {
            $reflexion = new \ReflectionClass($entity);
            $classEntity = $reflexion->getShortName();
            $table_name = strtolower($reflexion->getShortName());
            $table_field = [];
            $entity_object = null;
            $method = null;
            foreach ($reflexion->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyName = $property->getName();
                $table_field[] = $propertyName;
            }
            if($entity_relation){
                $object = $reflexion->newInstance();
                $class_name = strtolower($classEntity);
                $method = $reflexion->getMethod('getTableName');
                $method->setAccessible(true);
                $get_table_name = $method->invoke($object);
                //
                $this_reflexion = new \ReflectionClass($this);
                $object = $this_reflexion->newInstance();
                if ($this_reflexion->hasMethod($get_table_name)){
                    $method = $this_reflexion->getMethod($get_table_name);
                    $method->setAccessible(true);
                } else if ($this_reflexion->hasMethod(($class_name))){
                    $method = $this_reflexion->getMethod($class_name);
                    $method->setAccessible(true);
                }else{
                    throw new \Exception('You should implement a relation method for the ' . $class_name .' from class '. $classEntity);
                }
                $entity_object = $method->invoke($object);
            }elseif ($entity_name){
                $object = $reflexion->newInstance();
                $method = $reflexion->getMethod('getTableName');
                $method->setAccessible(true);
                $table_name = $method->invoke($object);
            }
            return (object)[
                'table' => $table_name,
                'fields' => $table_field,
                'entity_object' => $entity_object,
                'class' => $classEntity,
            ];
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }
}
