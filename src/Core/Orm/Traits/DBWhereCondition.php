<?php

namespace Wepesi\Core\Orm\Traits;

use PhpParser\Node\Expr\Cast\Object_;
use Wepesi\Core\Orm\WhereQueryBuilder\WhereBuilder;

/**
 *
 */
trait DBWhereCondition
{
    /**
     * @param WhereBuilder $whereBuilder
     * @return array|void
     */
    public function condition(WhereBuilder $whereBuilder)
    {
        $where = $whereBuilder->generate();
        if (count($where) == 0) return;
        $params = [];

        /**
         * defined comparison operator to avoid error while passing operation witch does not exist
         */
        $logicalOperator = ["or", "not"];
        // check if the array is multidimensional array
        $len = count($where);
        $where_condition_string = '';
        $index = 1;
        $fieldValue = [];
        //
        foreach ($where as $object) {
            $notComparison = null;
            // check the field exist and defined by default one
            $where_condition_string .= $object->field_name . $object->comparison . " ? ";
            $field_value[] = $object->field_value;

            $params[$object->field_name] = $object->field_value;
            if ($index < $len) {
                $where_condition_string .= $object->operator;
            }
            $index++;
        }
        return [
            "field" => "WHERE " . $where_condition_string,
            "value" => $field_value,
            "params" => $params
        ];
    }
}