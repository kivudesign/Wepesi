<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database\Providers\Contracts;

/**
 * @package Wepesi\Core\Database
 * @template T
 */
interface WhereConditionContracts
{
    /**
     * @param int|string $field_comparison
     * @return WhereConditionContracts
     */
    public function isGreaterThan(int|string $field_comparison): WhereConditionContracts;

    /**
     * @param $field_comparison
     * @return WhereConditionContracts
     */
    public function isGreaterEqualThan(int|string $field_comparison): WhereConditionContracts;

    /**
     * @param $field_comparison
     * @return WhereConditionContracts
     */
    public function isLessThan(int|string $field_comparison): WhereConditionContracts;

    /**
     * @param $field_comparison
     * @return WhereConditionContracts
     */
    public function isLessEqualThan(int|string $field_comparison): WhereConditionContracts;

    /**
     * @param $field_comparison
     * @return WhereConditionContracts
     */
    public function isEqualTo(int|string $field_comparison): WhereConditionContracts;

    /**
     * @param int|string $field_comparison
     * @return WhereConditionContracts
     */
    public function isDifferentTo(int|string $field_comparison): WhereConditionContracts;

    /**
     * @param int|string $field_comparison
     * @return WhereConditionContracts
     */
    public function isNotEqualTo(int|string $field_comparison): WhereConditionContracts;

    /**
     * @param int|string $field_comparison
     * @return WhereConditionContracts
     */
    public function isLike(int|string $field_comparison): WhereConditionContracts;
}