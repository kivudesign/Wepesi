<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Traits;


/**
 *
 */
trait ExceptionTraits
{

    /**
     * @param $ex
     * @return array
     */
    protected function exception($ex): array
    {
        return ['exception' => $ex->getMessage()];
    }
}