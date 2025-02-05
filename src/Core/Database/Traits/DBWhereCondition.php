<?php

namespace Wepesi\Core\Database\Traits;

use Wepesi\Core\Database\Providers\Contracts\WhereBuilderContracts;

/**
 * @package Wepesi\Core\Database
 * @template DBWhereCondition
 * *
 */
trait DBWhereCondition
{
    /**
     * @param WhereBuilderContracts|array $whereBuilder
     * @return array|void
     */
    public function getCondition(WhereBuilderContracts|array $whereBuilder)
    {
        $isObjectBuilder = false;
        $where = $whereBuilder;
        if ($whereBuilder instanceof WhereBuilderContracts) {
            $isObjectBuilder = true;
            $where = $whereBuilder->generate();
            $len = count($where);
        } else {
            $where = is_array($where[0]) ? $where : [$where];
            $len = count($where);
        }
        if (count($where) == 0) return;
        $fieldValues = [];
        $params = [];
        // attribute for array conditions
        $logicalOperators = ['or', 'not'];
        $comparisonOperator = '=';
        //
        $index = 1;
        $whereConditionParams = '';
        foreach ($where as $object) {
            if ($isObjectBuilder) {
                // check the field exist and defined by default one
                $whereConditionParams .= $object->field_name . $object->comparison . ' ? ';
                $fieldValues[] = $object->field_value;

                $params[$object->field_name] = $object->field_value;
                if ($index < $len) {
                    $whereConditionParams .= $object->operator;
                }
            } else {
                $logicalOperator = ' and ';
                $notComparison = '';
                // check if there is a logical operator `or`||`and`
                if (isset($object[3])) {
                    // check id the defined operation exists in our defined tables
                    $logicalOperator = in_array(strtolower($object[3]), $logicalOperators) ? $object[3] : ' and ';
                    if (trim($logicalOperator) === 'not') {
                        $notComparison = ' not ';
                    }
                }
                $fieldValue = $object[2] ?? '';
                $fieldName = strlen(trim($object[0])) > 0 ? trim($object[0]) : 'id';
                $params[$fieldName] = $fieldValue;
                $fieldValues[] = $fieldValue;
                // check the field exist and defined by default one
                $whereConditionParams .= $notComparison . $fieldName . $comparisonOperator . ' ? ';
                //
                if ($index < $len) {
                    if ($logicalOperator != 'not') {
                        $whereConditionParams .= $logicalOperator;
                    }
                }
            }
            $index++;
        }

        return [
            'field' => 'WHERE ' . $whereConditionParams,
            'value' => $fieldValues,
            'params' => $params
        ];
    }
}