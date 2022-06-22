<?php

namespace Wepesi\Core\Orm;

class DBDelete
{
    private \PDO $_pdo;
    private string $table;
    private array $_where;
    private  ?string $_error;
    private ?int $_count = 0;
    private ?string $_results;

    function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->_pdo = $pdo;
        $this->_where=[];
    }
    /**
     * TODO make where function more expressive and easy to use with a better structure. on v3
     * be able to make a where with field name as a kye
     * [
     * "field"=>"field_name",
     * "Op"=>"or,and,=,>,<,<>,.."
     * "value"=>"field_value"
     * ]
     **/
    function where(array $where = [])
    {
        /**
         * select WHERE format
         * [
         *  [field,comparisonOperator,value,logicOperator]
         * ]
         * eg:[
         *  ["name","=","john","and"]
         * ]
         */
        if (count($where)) {
            $params = [];
            /**
             * defined comparion operator to avoid error while assing operation witch does not exist
             */
            $logicalOperator = ["or", "not"];
            $default_logical_operator = " and ";
            // chech if the array is multidimensional array
            $where = is_array($where[0]) ? $where : [$where];
            $whereLen = count($where);
            //
            $jointure_Where_Condition = null;
            $defaultComparison = "=";
            $lastIndexWhere = 1;
            $fieldValue = [];
            //
            foreach ($where as $WhereField) {
                $notComparison = null;
                // check if there is a logical operator `or`||`and`
                if (isset($WhereField[3])) {
                    // check id the defined operation exist in our defined tables
                    $default_logical_operator = in_array(strtolower($WhereField[3]), $logicalOperator) ? $WhereField[3] : " and ";
                    if ($default_logical_operator === "not") {
                        $notComparison = " not ";
                    }
                }
                // check the field exist and defined by default one
                $where_field_name = strlen(trim($WhereField[0])) > 0 ? trim($WhereField[0]) : "id";
                $jointure_Where_Condition .=  $notComparison.$where_field_name.$defaultComparison." ? ";
                $where_field_value = $WhereField[2] ?? null;
                $fieldValue[]=$where_field_value;
//
                $params[$where_field_name]=$where_field_value;
                if ($lastIndexWhere < $whereLen) {
                    if ($default_logical_operator != "not") {
                        $jointure_Where_Condition .= $default_logical_operator;
                    }
                }
                $lastIndexWhere++;
            }
            $this->_where = [
                "field" => "WHERE ".$jointure_Where_Condition,
                "value" => $fieldValue,
                "params" => $params
            ];
        }
        return $this;
    }

    /**
     * @param $sql
     * @param array $params
     * @return $this
     * this module is use to execute sql request
     */
    private function query($sql, array $params = [])
    {
        $q = new DBQuery($this->_pdo, $sql, $params);
        $this->_results = $q->result();
        $this->_count = $q->rowCount();
        $this->_error = $q->getError();
    }

    /**
     * @return bool
     * use this module to detele and existing row record
     */
    private function delete()
    {
        $where = $this->_where['field'] ??"";
        $params = $this->_where['params'] ?? [];
        $sql = "DELETE FROM $this->table $where";
        return $this->query($sql, $params);
    }

    /**
     * @return bool
     * return result after a request select
     */
    function result()
    {
        $this->delete();
        return $this->_results;
    }
    // return an error status when an error occure while doing an querry
    function error()
    {
        return $this->_error;
    }

    /**
     * @return int
     * return counted rows of a select query
     */
    function count()
    {
        return $this->_count;
    }
}