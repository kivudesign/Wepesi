<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database\Providers\Contracts;
/**
 * @package Wepesi\Core\Database
 * @template T
 */
interface WhereBuilderContracts
{
    /**
     * @param WhereConditionContracts $where_condition
     * @return mixed
     */
    public function orOption(WhereConditionContracts $where_condition): WhereBuilderContracts;

    /**
     * @param WhereConditionContracts $where_condition
     * @return mixed
     */
    public function andOption(WhereConditionContracts $where_condition): WhereBuilderContracts;

    /**
     * @param WhereBuilderContracts $where_builder
     * @return mixed
     */
    public function groupOption(WhereBuilderContracts $where_builder): array;
}
