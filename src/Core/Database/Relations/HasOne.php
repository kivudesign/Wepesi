<?php

namespace Wepesi\Core\Database\Relations;

use Wepesi\Core\Database\EntityModel\Provider\Contract\EntityInterface;

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