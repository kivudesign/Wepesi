<?php

namespace Wepesi\Core\Orm\Relations;

use Wepesi\Core\Orm\EntityModel\Provider\Contract\EntityInterface;


/**
 *
 */
class HasMany extends BaseRelation
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
