<?php

namespace Wepesi\Core\BaseEntityModel;

use Exception;
use ReflectionClass;

/**
 *
 */
trait EntityReflexion
{
    /**
     * @param Entity $entity
     * @return array
     */
    protected function getEntityName(Entity $entity): array
    {
        try {
            $class = new ReflectionClass($entity);
            $object = $class->newInstance();
            $method = $class->getMethod('getTableName');
            $method->setAccessible(true);
            return ['table' => $method->invoke($object)];
        } catch (Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }
}