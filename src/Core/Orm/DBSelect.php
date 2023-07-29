<?php

namespace Wepesi\Core\Orm;

use Wepesi\Core\Orm\Provider\DbProvider;
use Wepesi\Core\Orm\Traits\DBWhereCondition;
use Wepesi\Core\Orm\WhereQueryBuilder\WhereBuilder;

/**
 *
 */
class DBSelect extends DbProvider
{
    /**
     * @var string
     */
    private string $table;
    /**
     * @var string|null
     */
    private ?string $action;
    /**
     * @var array
     */
    private array $where;
    /**
     * @var array|string[]
     */
    private array $_fields;
    /**
     * @var array|string[]
     */
    private array $_results;
    /**
     * @var array|string[]
     */
    private array $_join_comparison_sign;
    /**
     * @var string|null
     */
    private string $_leftJoin;
    /**
     * @var string|null
     */
    private string $_rightJoin;
    /**
     * @var string|null
     */
    private string $_join;
    /**
     * @var string|null
     */
    private string $orderBy;
    /**
     * @var string|null
     */
    private string $groupBY;
    /**
     * @var string
     */
    private string $_limit;
    /**
     * @var string
     */
    private string $_offset;
    /**
     * @var string
     */
    private string $_dsc;
    /**
     * @var string
     */
    private string $_asc;
    /**
     * @var string
     */
    private string $_between;
    use DBWhereCondition;

    /**
     *
     * @param \PDO $pdo
     * @param string $table
     * @param string|null $action
     */
    public function __construct(\PDO $pdo, string $table, string $action = null)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->action = $action;
        $this->where = [];
        $this->_results = [];
        $this->_leftJoin = '';
        $this->_rightJoin = '';
        $this->_join = '';
        $this->orderBy = '';
        $this->groupBY = '';
        $this->_error = '';
        $this->_count = 0;
        $this->_fields = ['keys' => '*'];
        $this->_limit = '';
        $this->_offset = '';
        $this->_dsc = '';
        $this->_asc = '';
        $this->_between = '';
        $this->_join_comparison_sign = ['=', '>', '<', '!=', '<>'];
    }

    /**
     * @param WhereBuilder $where_builder
     * @return $this
     */
    public function where(WhereBuilder $where_builder): DBSelect
    {
        $this->where = $this->condition($where_builder);
        return $this;
    }

    /**
     * @return $this
     */
    public function between(string $field, array $value): DBSelect
    {
        if (count($value) == 2 && !is_array($value[0]) && !is_array($value[1])) {
            $this->_between = " $field between ? AND ?";
            $this->where['value'][] = $value[0];
            $this->where['value'][] = $value[1];
        }
        return $this;
    }

    /**
     *
     * @param array $fields
     * @return $this
     */
    public function field(array $fields = []): DBSelect
    {
        if (count($fields) > 0) {
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
    public function groupBY(string $field): DBSelect
    {
        if ($field) $this->groupBY = " group by $field";
        return $this;
    }

    /**
     *
     * @param string $order
     * @return $this
     */
    public function orderBy(string $order): DBSelect
    {
        if ($order) $this->orderBy = " order by $order";
        return $this;
    }

    /**
     * @return $this
     */
    public function random(): DBSelect
    {
        $this->orderBy = ' order by RAND()';
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function ASC(): DBSelect
    {
        $this->_asc = ' ASC ';
        $this->_dsc = '';
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function DESC(): DBSelect
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
    public function limit(int $limit): DBSelect
    {
        if ($limit) $this->_limit = " LIMIT $limit";
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): DBSelect
    {
        if ($offset) $this->_offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     *
     * @return array
     * execute query to get result
     */
    public function result(): array
    {
        $this->build();
        return $this->result;
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
     */
    private function count_all(): void
    {
        $WHERE = $this->where['field'] ?? '';
        $params = $this->where['value'] ?? [];
        $sql = "SELECT COUNT(*) as count FROM {$this->table} " . $WHERE;
        $this->query($sql, $params);
    }

    /**
     *
     */
    private function select()
    {
        $fields = $this->_fields['keys'] ?? '*';
        //
        $WHERE = $this->where['field'] ?? '';
        if (strlen($WHERE) > 1 && strlen($this->_between) > 1) {
            $WHERE .= " AND $this->_between";
        } else if (strlen($this->_between) > 1) {
            $WHERE = " WHERE $this->_between";
        }
        $params = $this->where['value'] ?? [];
        //
        $_jointure = '';
        //
        $sql = "SELECT {$fields} FROM {$this->table} {$_jointure} " . $WHERE . $this->groupBY . $this->orderBy . $this->_asc . $this->_dsc . $this->_limit . $this->_offset;
        $this->query($sql, $params);
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
}
