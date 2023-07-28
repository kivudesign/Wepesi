<?php

namespace Wepesi\Core\Orm;

use Wepesi\Core\Orm\Provider\DbProvider;

class DBSelect extends DbProvider
{
    private string $table;
    private ?string $action;
    private array $_where ,$_fields ,$_results ,$_join_comparison_sign;
    private ?string $_leftJoin,$_rightJoin,$_join,$orderBy,$groupBY;
    private string $_limit,$_offset,$_dsc,$_asc;
    private string $_between;

    /**
     *
     * @param \PDO $pdo
     * @param string $table
     * @param string|null $action
     */
    function __construct(\PDO $pdo, string $table, string $action = null)
    {
        $this->table = $table;
        $this->_pdo = $pdo;
        $this->action = $action;
        $this->_where  = [];
        $this->_results = [];
        $this->_leftJoin = null;
        $this->_rightJoin = null;
        $this->_join = null;
        $this->orderBy = null;
        $this->groupBY = null;
        $this->_error = '';
        $this->_count = 0;
        $this->_fields = ['keys'=>'*'];
        $this->_limit = '';
        $this->_offset = '';
        $this->_dsc = '';
        $this->_asc = '';
        $this->_between = '';
        $this->_join_comparison_sign = ['=', '>', '<', '!=', '<>'];
    }

    /**
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    function where(array $params = []): DBSelect
    {
        if (count($params)) {
            $comparisonOperator = ['<', '<=', '>', '>=', '<>', '!=', 'like'];
            // defined logical operator
            $logicalOperator = ['or', 'not'];
            // chech if the array is multidimensional array
            $key = array_keys($params);
            $key_exist = is_string($key[0]);
            if ($key_exist) {
                throw new \Exception('bad format, for where data');
            }
            $where = is_array($params[0]) ? $params : [$params];
            $whereLen = count($where);
            //
            $jointuresWhereCondition = '';
            $defaultComparison = '=';
            $lastIndexWhere = 1;
            $fieldValue = [];
            //
            foreach ($where as $WhereField) {
                $defaultLogical = ' AND ';
                $notComparison = null;
                // check if there is a logical operator `or`||`and`
                if (isset($WhereField[3])) {
                    // check id the defined operation exist in our defined tables
                    $defaultLogical = in_array(strtolower($WhereField[3]), $logicalOperator) ? $WhereField[3] : ' and ';
                    if ($defaultLogical === 'not') {
                        $notComparison = ' not ';
                    }
                }
                // check the field exist and defined by default one
                $_WhereField = strlen($WhereField[0]) > 0 ? $WhereField[0] : 'id';
                // check if comparison  exist on the array
                $defaultComparison = in_array($WhereField[1], $comparisonOperator) ? $WhereField[1] : '=';
                $jointuresWhereCondition .= " {$notComparison} {$_WhereField} {$defaultComparison}  ? ";
                $valueTopush = $WhereField[2] ?? null;
                $fieldValue[] = $valueTopush;
                if ($lastIndexWhere < $whereLen) {
                    if ($defaultLogical != 'not') {
                        $jointuresWhereCondition .= $defaultLogical;
                    }
                }
                $lastIndexWhere++;
            }
            $this->_where ['field'] = " WHERE {$jointuresWhereCondition} ";
            $this->_where['value'] = $fieldValue;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function between(string $field,array $value): DBSelect
    {
        if(count($value)==2 && !is_array($value[0]) && !is_array($value[1])){
            $this->_between = " $field between ? AND ?";
            $this->_where['value'][]=$value[0];
            $this->_where['value'][]=$value[1];
        }
        return $this;
    }
    /**
     *
     * @param array $fields
     * @return $this
     */
    function field(array $fields = []): DBSelect
    {
        if (count($fields)>0) {
            $keys = $fields;
            $values = null;
            $this->_fields = [
                'keys' => implode(',', $keys),
                'values' => $values
            ];
        }
        return $this;
    }

    /**
     *
     * @param array $group
     * @return $this
     */
    function groupBY(string $field): DBSelect
    {
        if ($field) $this->groupBY = " group by $field";
        return $this;
    }

    /**
     *
     * @param string $order
     * @return $this
     */
    function orderBy(string $order): DBSelect
    {
        if ($order) $this->orderBy = " order by $order";
        return $this;
    }

    function random(): DBSelect
    {
        $this->orderBy = ' order by RAND()';
        return $this;
    }

    /**
     *
     * @return $this
     */
    function ASC(): DBSelect
    {
        $this->_asc = ' ASC ';
        $this->_dsc = '';
        return $this;
    }

    /**
     *
     * @return $this
     */
    function DESC(): DBSelect
    {
        $this->_asc = '';
        $this->_dsc = ' DESC ';
        return $this;
    }

    /**
     *
     * @param int $limit
     * @return $this
     */
    function limit(int $limit): DBSelect
    {
        if($limit)$this->_limit = " LIMIT $limit";
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    function offset(int $offset): DBSelect
    {
        if($offset)$this->_offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * @param string $table_name
     * @param array $onParameters : this represents the field
     * @return $this|false
     * this module allow to do a single left join, in case of multiple join, it's recommend use to use the `query` method with is better
     */
    private function leftJoin(string $table_name, array $onParameters = [])
    {
        $on = null;
        if (count($onParameters) == 3) {
            $_field1 = $onParameters[0];
            $_operator = $onParameters[1];
            $_field2 = $onParameters[2];
            if (in_array($_operator, $this->_join_comparison_sign)) $on = " ON {$_field1}{$_operator}{$_field2} ";
        }
        if (!$table_name) return false;
        $this->_leftJoin = " LEFT {$table_name} {$on} ";
        return $this;
    }

    /**
     * @param string $table_name
     * @param array $onParameters
     * @return $this|false
     */
    private function rightJoin(string $table_name, array $onParameters = [])
    {
        try {
            $on = null;
            if (count($onParameters) == 3) {
                $_field1 = $onParameters[0];
                $_operator = $onParameters[1];
                $_field2 = $onParameters[2];
                if (in_array($_operator, $this->_join_comparison_sign)) $on = " ON {$_field1}{$_operator}{$_field2} ";
            }
            if (!$table_name) return false;
            $this->_leftJoin = " RIGHT {$table_name} {$on} ";
            return $this;
        } catch (\Exception $ex) {
            $this->_error = true;
        }
    }

    /**
     * @param string $table_name
     * @param array $onParameters
     * @return $this|false
     * this module wil help only join single(one) table
     * for multiple join better to use query method for better perfomances
     */
    private function join(string $table_name, array $onParameters = [])
    {
        try {
            $on = null;
            if (count($onParameters) == 3) {
                $_field1 = $onParameters[0];
                $_operator = $onParameters[1];
                $_field2 = $onParameters[2];
                if (in_array($_operator, $this->_join_comparison_sign)) $on = " ON {$_field1}{$_operator}{$_field2} ";
            }
            if (!$table_name) return false;
            $this->_leftJoin = " JOIN {$table_name} {$on} ";
            return $this;
        } catch (\Exception $ex) {
            $this->_error = true;
        }
    }

    /**
     *
     */
    private function select()
    {
        $fields = $this->_fields['keys'] ?? '*';
        //
        $WHERE = $this->_where['field']?? '';
        if(strlen($WHERE)>1 && strlen($this->_between)>1){
            $WHERE.= " AND $this->_between";
        }else if(strlen($this->_between)>1){
            $WHERE =" WHERE $this->_between";
        }
        $params = $this->_where['value'] ?? [];
        //
        $_jointure = '';
        //
        $sql = "SELECT {$fields} FROM {$this->table} {$_jointure} " . $WHERE . $this->groupBY . $this->orderBy . $this->_asc . $this->_dsc . $this->_limit . $this->_offset;
        $this->query($sql, $params);
    }

    /**
     *
     */
    private function count_all():void
    {
        $WHERE = $this->_where['field'] ?? '';
        $params = $this->_where['value'] ?? [];
        $sql = "SELECT COUNT(*) as count FROM {$this->table} " . $WHERE;
        $this->query($sql, $params);
    }

    /**
     *
     */
    private function build()
    {
        if ($this->action && $this->action == 'count') {
            $this->count_all();
        } else {
            $this->select();
        }
    }

    /**
     *
     * @return array
     * execute query to get result
     */
    function result(): array
    {
        $this->build();
        return $this->_results;
    }
}