<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database;

use Wepesi\Core\Database\Providers\BaseEntity;
use Wepesi\Core\Database\Providers\Contracts\EntityContracts;

/**
 * @template Entity of EntityContracts
 * @template-extends BaseEntity<Entity>
 */
abstract class Entity extends BaseEntity implements EntityContracts{}