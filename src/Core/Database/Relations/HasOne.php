<?php

namespace Wepesi\Core\Database\Relations;

use Wepesi\Core\Database\Providers\BaseRelation;
use Wepesi\Core\Database\Providers\Contracts\EntityContracts;

/**
 *
 */
class HasOne extends BaseRelation
{
    /**
     * @param EntityContracts $entity_parent
     * @param EntityContracts $entity_child
     */
    public function __construct(EntityContracts $entity_parent, EntityContracts $entity_child)
    {
        parent::__construct($entity_parent, $entity_child);
    }
}