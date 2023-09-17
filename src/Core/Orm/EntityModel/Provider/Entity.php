<?php

namespace Wepesi\Core\Orm\EntityModel\Provider;

use Wepesi\Core\Orm\EntityModel\EntityReflexion;
use Wepesi\Core\Orm\EntityModel\Provider\Contract\EntityInterface;
use Wepesi\Core\Orm\DB;
use Wepesi\Core\Orm\Relations\HasMany;
use Wepesi\Core\Orm\Relations\HasOne;
use Wepesi\Core\Orm\WhereQueryBuilder\WhereBuilder;

/**
 *
 */
abstract class Entity implements EntityInterface
{
    /**
     * @var DB
     */
    private DB $db;
    /**
     * @var array
     */
    private array $include_entity;
    /**
     * @var array|mixed
     */
    private array $param;
    use EntityReflexion;

    /**
     *
     */
    public function __construct()
    {
        $this->db = DB::getInstance();
        $this->include_entity = [];
        $this->param = [];
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        try {
            $table = $this->getTableName();

            if (count($this->include_entity) > 0) {
                foreach ($this->include_entity as $value) {
                    $table .= ' ' . $value['join'] . ' JOIN ' . $value['table'];
                    if (isset($value['on'])) {
                        $table .= $value['on'];
                    }
                }
            }
            $query = $this->db->get($table);
            if (isset($this->param['where'])) {
                $query->where($this->param['where']);
            }
            if (isset($this->param['limit'])) {
                $query->limit($this->param['limit']);
            }
            if (isset($this->param['offset'])) {
                $query->offset($this->param['offset']);
            }
            if (isset($this->param['fields'])) {
                $query->field($this->param['fields']);
            }
            if (isset($this->param['ascending'])) {
                $query->{$this->param['ascending']}();
            }

            if (isset($this->param['orderby'])) {
                $query->orderBy($this->param['orderby']);
            }
            if (count($this->include_entity) > 0) {
                $query->include($this->include_entity);
            }
            $result = $query->result();
            $this->param = [];
            if ($this->db->error()) {
                throw new \Exception($this->db->error());
            }
            return $result;
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @return string
     */
    private function getTableName(): string
    {
        return trim($this->getName()) !== '' ? trim($this->getName()) : $this->getClassDefinition($this)->table;
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return '';
    }

    /**
     * @param WhereBuilder $where
     * @return $this
     */
    public function where(WhereBuilder $where): Entity
    {
        $this->param['where'] = $where;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): Entity
    {
        $this->param['limit'] = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): Entity
    {
        $this->param['offset'] = $offset;
        return $this;
    }

    /**
     * @param string $field_name provide the field name to ordered by
     * @return $this
     */
    public function orderby(string $field_name): Entity
    {
        $this->param['orderby'] = $field_name;
        return $this;
    }

    /**
     * @param EntityInterface $entityName
     * @param bool $inner
     * @return $this
     */
    public function include(EntityInterface $entityName, bool $inner = false): Entity
    {
        try {
            $entity_table_object = $this->getClassDefinition($entityName,true);
            if (is_array($entity_table_object) && isset($entity_table_object['exception'])) {
                throw new \Exception($entity_table_object['exception']);
            }
            $entity_object = $entity_table_object->entity_object;
            $relation = [
                'entity' => $entity_object ?? $entity_table_object->table,
                'table' => $entity_table_object->table,
                'join' => $inner ? 'INNER' : 'LEFT',
            ];

            $entity_relation = $entity_object->getRelation();
            $primary_key = $this->getTableName() . '.' . $entity_relation->primary_key;
            $foreign_key = $entity_table_object->table . '.' . $entity_relation->foreign_key;
            $relation['on'] = ' ON ' . $primary_key . '=' . $foreign_key;

            $this->include_entity [] = $relation;
        } catch (\Exception $ex) {
            print_r(['exception' => $ex->getMessage()]);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function error(): array
    {
        return ['exception' => $this->db->error()];
    }

    /**
     * @return array
     */
    public function findOne(): array
    {
        try {

            $query = $this->db->get($this->getTableName())->limit(1);

            if (isset($this->param['where'])) {
                $query->where($this->param['where']);
            }
            $result = $query->result();
            $this->param = [];
            if ($this->db->error()) {
                throw new \Exception($this->db->error());
            }
            return $result;
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }
    /**
     * @return array
     */
    public function count(): array
    {
        try {

            $query = $this->db->count($this->getTableName());
            if (isset($this->param['where'])) {
                $query->where($this->param['where']);
            }

            $result = $query->result();
            $this->param = [];
            if ($this->db->error()) {
                throw new \Exception($this->db->error());
            }
            return $result;
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }
    /**
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields): Entity
    {
        $this->param['fields'] = $fields;
        return $this;
    }

    /**
     * @return $this
     */
    public function desc(): Entity
    {
        $this->param['ascending'] = 'DESC';
        return $this;
    }

    /**
     * @return $this
     */
    public function asc(): Entity
    {
        $this->param['ascending'] = 'ASC';
        return $this;
    }

    /**
     * @return array
     */
    public function delete(): array
    {
        try {

            $query = $this->db->delete($this->getTableName());
            $result = $query->result();
            if (isset($this->param['where'])) {
                $query->where($this->param['where']);
            }
            $this->param = [];
            if ($this->db->error()) {
                throw new \Exception($this->db->error());
            }
            return $result;
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @param array $fields
     * @return array
     */
    public function save(array $fields): array
    {
        try {
            $result = $this->db->insert($this->getTableName())->field($fields)->result();
            if ($this->db->error()) {
                throw new \Exception($this->db->error());
            }
            $id = $this->db->lastId();
            if ($id < 1) {
                throw new \Exception('no field has been recorded');
            }
            return $result;
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @return array
     */
    public function update(): array
    {
        try {
            $field = [];
            $query = $this->db->update($this->getTableName());
            if (isset($this->param['where'])) {
                $query->where($this->param['where']);
            }
            if (isset($this->param['fields'])) {
                $query->field($this->param['fields']);
            }
            $result = $query->result();
            $this->param = [];
            if ($query->error()) {
                throw new \Exception($query->error());
            }
            return $result;
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @return array
     */
    protected function validate(): array
    {
        return [];
    }

    /**
     * @param EntityInterface $entity
     * @return object|HasMany
     */
    protected function hasMany(EntityInterface $entity): object
    {
        return (new HasMany($this, $entity));
    }

    /**
     * @param EntityInterface $entity
     * @return object|HasOne
     */
    protected function hasOne(EntityInterface $entity): object
    {
        return (new HasOne($this, $entity));
    }
}