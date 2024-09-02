<?php

namespace Wepesi\Core\Validation\Providers\Contracts;

/**
 *
 */
interface MessageBuilderContracts
{
    /**
     * @param string $value
     * @return MessageBuilderContracts
     */
    public function message(string $value): MessageBuilderContracts;

    /**
     * @param string $value
     * @return MessageBuilderContracts
     */
    public function type(string $value): MessageBuilderContracts;

    /**
     * @param string $value
     * @return MessageBuilderContracts
     */
    public function label(string $value): MessageBuilderContracts;

    /**
     * @param string $value
     * @return MessageBuilderContracts
     */
    public function limit(string $value): MessageBuilderContracts;
}