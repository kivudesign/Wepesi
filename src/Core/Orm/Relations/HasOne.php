<?php

namespace Wepesi\Core\Orm\Relations;

use Wepesi\Core\BaseEntityModel\Provider\Contract\EntityInterface;

/**
 *
 */
class HasOne extends BaseRelation
{
    /**
     * @param EntityInterface $entity_parent
     * @param EntityInterface $entity_child
     */
    public function __construct(EntityInterface $entity_parent, EntityInterface $entity_child)
    {
        parent::__construct($entity_parent, $entity_child);
    }
}