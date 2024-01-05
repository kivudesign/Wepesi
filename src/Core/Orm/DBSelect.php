<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Orm;

use Exception;
use PDO;
use Wepesi\Core\Escape;
use Wepesi\Core\Orm\Provider\DbProvider;
use Wepesi\Core\Orm\Traits\DBWhereCondition;
use Wepesi\Core\Orm\WhereQueryBuilder\WhereBuilder;

/**
 *
 */
class DBSelect extends DbProvider
{
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
    private string $ascending;
    /**
     * @var string
     */
    private string $_between;

    use DBWhereCondition;

    /**
     *
     * @param PDO $pdo
     * @param string $table
     * @param string|null $action
     */
    public function __construct(PDO $pdo, string $table, string $action = null)
    {
        $this->table = $table;
        $this->pdo = $pdo;
        $this->action = $action;
        $this->where = [];
        $this->_results = [];
        $this->orderBy = '';
        $this->groupBY = '';
        $this->_error = '';
        $this->_count = 0;
        $this->_fields = ['keys' => '*'];
        $this->_limit = '';
        $this->_offset = '';
        $this->ascending = '';
        $this->_between = '';
        $this->include_object = [];
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
     * @param string $field
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
        $this->ascending = ' ASC ';
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function DESC(): DBSelect
    {
        $this->ascending = ' DESC ';
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
        return (!isset($this->result['exception']) && count($this->include_object) > 0 && count($this->result) > 0) ? $this->formatData($this->result) : $this->result;
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
        if ($this->orderBy == '' && $this->ascending != '') {
            $this->result['exception'] = 'You should provide the order by which field name to which field you to apply the `' . $this->ascending . '`.';
        } else {
            $sql = "SELECT {$fields} FROM {$this->table} " . $WHERE . $this->groupBY . $this->orderBy . $this->ascending . $this->_limit . $this->_offset;
            $this->query($sql, $params);
        }
    }

    /**
     * @param array $result
     * @return array
     */
    protected function formatData(array $result): array
    {
        try {
            $entity_object = [];
            foreach ($this->include_object as $include_intity_object) {
                $entity_object[] = $include_intity_object['entity'];
            }
            $parent_entity = [];
            $children_entity = [];
            $other_entity = [];
            $arr_rel = [];
            foreach ($entity_object as $entity) {
                $relation = $entity->getRelation();
                if (!in_array($relation->parent, $parent_entity) && count($parent_entity) == 0) {
                    $parent_entity[] = $relation->parent;
                } else if (!in_array($relation->parent, $parent_entity)) {
                    $other_entity[] = $relation->parent;
                }
                switch ($relation->type) {
                    case 'HasMany' :
                        if (!in_array($relation->child, $children_entity)) {
                            $children_entity[] = $relation->child;
                        } else if (!in_array($relation->child, $other_entity)) {
                            $other_entity[] = $relation->child;
                        }
                        break;
                    case 'BelongTo':
                        break;
                    case 'HasOne':
                        $other_entity[] = $relation->child;
                        break;
                }
            }
            return $this->buildStructure($result, $parent_entity[0], $children_entity, $other_entity);

        } catch (Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @param $result
     * @return array
     */
    protected function buildStructure($result, string $parent_entity, array $children_entity, array $other_entity): array
    {
        try {
            $data_table = $this->getTableStructure($result);
            $list_table = $data_table['list_table'];
            $parent_ids = $data_table['parent_ids'];
            $result = $data_table['result'];

            $data_result = [];
            $parent_table = [];
            $others_tables_name = $other_entity;
            $parent_table_name = $parent_entity;
            $children_table_name = $children_entity;

            foreach ($result as $index => $data_parent_table) {
                $first_row = $data_parent_table[0];
                $parent_keys = array_keys($first_row);

                //
                $filter_parent_field = array_filter($parent_keys, function ($item) use ($parent_table_name) {
                    if (explode('.', $item)[0] == $parent_table_name) {
                        return $item;
                    }
                });

                //
                $parent_table['id'] = $parent_ids[$index];
                foreach ($filter_parent_field as $child_key) {
                    $key = explode('.', $child_key)[1];
                    $parent_table[$key] = $first_row[$child_key];
                }

                /**
                 * if there is table and there is a parent and child table base on the relation definition
                 */

                foreach ($children_table_name as $child_table_name) {
                    $parent_table[$child_table_name] = $this->getExtractedChild($data_parent_table, $child_table_name, $parent_keys);
                }
                // for class that the relation as been defined as one, or not
                foreach ($others_tables_name as $other_table_name) {
                    $parent_table[$other_table_name] = $this->getExtractedUndefined($first_row, $other_table_name, $parent_keys);
                }
                $data_result[] = (object)$parent_table;
            }
            return $data_result;
        } catch (Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @param array $data_table
     * @return array
     */
    private function getTableStructure(array $data_table): array
    {
        $list_table = [];
        $parent_ids = array_keys($data_table);
        $data_table = (array_values($data_table));

        $item_keys = array_keys($data_table[0][0]);
        foreach ($item_keys as $index => $value) {
            if (is_int($value)) continue;

            $table_name = (explode('.', $value))[0];
            if (!in_array($table_name, $list_table, true)) {
                $list_table[] = $table_name;
            }
        }
        return [
            'list_table' => $list_table,
            'parent_ids' => $parent_ids,
            'result' => $data_table
        ];
    }

    /**
     * extract children for each parent table
     * @param array $data_parent_table
     * @param string $child_table_name
     * @param array $parent_keys
     * @return array
     *
     */
    private function getExtractedChild(array $data_parent_table, string $child_table_name, array $parent_keys): array
    {
        $filter_child_field = array_filter($parent_keys, function ($item) use ($child_table_name) {
            if (explode('.', $item)[0] == $child_table_name) {
                return $item;
            }
        });

        $child_tables = [];
        foreach ($data_parent_table as $data_child_table) {
            $child_table = [];
            $there_isDate = false;
            foreach ($filter_child_field as $child_key) {
                $key = explode('.', $child_key)[1];
                if ($data_child_table[$child_key]) {
                    $there_isDate = true;
                    $child_table[$key] = $data_child_table[$child_key];
                }
            }
            if ($there_isDate) {
                $child_tables[] = $child_table;
            }
        }
        $unique = Escape::removeDuplicateAssocArray($child_tables);

        return array_map(function ($item) {
            return (object)$item;
        }, $unique);
    }

    /**
     * @param array $first_row
     * @param string $parent_table_name
     * @param array $parent_keys
     * @return object
     */
    private function getExtractedUndefined(array $first_row, string $parent_table_name, array $parent_keys): object
    {
        $filter_field = array_filter($parent_keys, function ($item) use ($parent_table_name) {
            if (explode('.', $item)[0] == $parent_table_name) {
                return $item;
            }
        });
        $data_table = [];
        foreach ($filter_field as $child_key) {
            $key = explode('.', $child_key)[1];
            $data_table[$key] = $first_row[$child_key];
        }
        return (object)$data_table;
    }

    /**
     * @param $name
     * @param $arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }
    }

    /**
     * @param array $includes
     * @return DBSelect
     */
    private function include(array $includes): DBSelect
    {
        $this->include_object = $includes;
        return $this;
    }
}
