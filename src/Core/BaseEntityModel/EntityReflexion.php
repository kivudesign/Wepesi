<?php

namespace Wepesi\Core\BaseEntityModel;

use Wepesi\Core\BaseEntityModel\Provider\Contract\EntityInterface;

/**
 *
 */
trait EntityReflexion
{
    /**
     * @param EntityInterface $entity
     * @return array
     */
    private function getEntityName(EntityInterface $entity): array
    {
        try {
            $reflexion = new \ReflectionClass($entity);
            $object = $reflexion->newInstance();
            $method = $reflexion->getMethod('getTableName');
            $class_name = strtolower($reflexion->getShortName());
            $method->setAccessible(true);
            return ['table' => $method->invoke($object),'class' => $class_name];
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @param string $entity_array
     * @return array|mixed
     */
    private function getEntityRelationLink(array $entity_array)
    {
        try {
            $reflexion = new \ReflectionClass($this);
            $object = $reflexion->newInstance();
            $table_name = $entity_array['table'];
            $class_name = $entity_array['class'];
            if ($reflexion->hasMethod($table_name)){
                $method = $reflexion->getMethod($table_name);
                $method->setAccessible(true);
            } else if ($reflexion->hasMethod($class_name)){
                $method = $reflexion->getMethod($class_name);
                $method->setAccessible(true);
            }else{
                throw new \Exception('You should implement a relation method for the ' . $class_name);
            }
            return $method->invoke($object);
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage() . ' from class ' . $this->getClassDefinition($this)->class];
        }
    }

    /**
     * @param EntityInterface $entity
     * @return object|array
     */
    private function getClassDefinition($entity): object
    {
        try {
            $reflexion = new \ReflectionClass($entity);
            $class_name = $reflexion->getShortName();
            $table_name = strtolower($reflexion->getShortName());
            $table_field = [];
            foreach ($reflexion->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyName = $property->getName();
                $table_field[] = $propertyName;
            }
            return (object)[
                'table' => $table_name,
                'fields' => $table_field,
                'class' => $class_name
            ];
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }
}
