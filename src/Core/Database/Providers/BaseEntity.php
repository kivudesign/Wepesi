<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Database\Providers;

use Exception;
use Wepesi\Core\Database\Database;
use Wepesi\Core\Database\Providers\Contracts\EntityContracts;
use Wepesi\Core\Database\Relations\HasMany;
use Wepesi\Core\Database\Relations\HasOne;
use Wepesi\Core\Database\Traits\EntityReflexionTrait;
use Wepesi\Core\Database\WhereQueryBuilder\WhereBuilder;

/**
 * @package Wepesi\Core\Database
 * @template BaseEntity of EntityContracts
 * @template-implements EntityContracts<BaseEntity>
 */
abstract class BaseEntity implements EntityContracts
{
    /**
     * @var Database
     */
    private Database $db;

    /**
     * @var array
     */
    private array $include_entity;

    /**
     * @var array
     */
    private array $param;

    use EntityReflexionTrait;

    /**
     *
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
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

            foreach ($this->include_entity as $value) {
                $table .= ' ' . $value['join'] . ' JOIN ' . $value['table'];
                if (isset($value['on'])) {
                    $table .= $value['on'];
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
                throw new Exception($this->db->error());
            }
            return $result;
        } catch (Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @return string
     */
    private function getTableName(): string
    {
        $table_name = $this->getName() ? trim($this->getName()) : '';
        return strlen($table_name) > 0 ? $table_name : $this->extractEntityDefinition($this)->table;
    }

    /**
     * set/get table name set as class entity.
     * @return string|null
     */
    protected function getName(): ?string
    {
        return null;
    }

    /**
     * Build your array where
     * @param array $where
     * @return array
     */
    public function where(array $where): array
    {
        // To Do implement where condition for a simple condition.
        return [];
    }

    /**
     * @param int $limit
     * @return EntityContracts
     */
    public function limit(int $limit): EntityContracts
    {
        return $this->setParam('limit', $limit);
    }

    /**
     * @param string $key
     * @param $value
     * @return EntityContracts
     */
    private function setParam(string $key, $value): EntityContracts
    {
        $this->param[$key] = $value;
        return $this;
    }

    /**
     * @param int $offset
     * @return EntityContracts
     */
    public function offset(int $offset): EntityContracts
    {
        return $this->setParam('offset', $offset);
    }

    /**
     * @param string $field_name provide the field name to ordered by
     * @return EntityContracts
     */
    public function orderby(string $field_name): EntityContracts
    {
        return $this->setParam('orderby', $field_name);
    }

    /**
     * @param EntityContracts $entityName
     * @param bool $inner
     * @return EntityContracts
     */
    public function include(EntityContracts $entityName, bool $inner = false): EntityContracts
    {
        try {
            $entity_table_object = $this->getEntityRelation($entityName);
            if (is_array($entity_table_object) && isset($entity_table_object['exception'])) {
                throw new Exception($entity_table_object['exception']);
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

            $this->include_entity[] = $relation;
        } catch (Exception $ex) {
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
     * @param WhereBuilder $where
     * @return $this
     */
    public function buildWhere(WhereBuilder $where): EntityContracts
    {
        return $this->setParam('where', $where);
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
                throw new Exception($this->db->error());
            }
            return $result;
        } catch (Exception $ex) {
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
                throw new Exception($this->db->error());
            }
            return $result;
        } catch (Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
    }

    /**
     * @param array $fields
     * @return EntityContracts
     */
    public function fields(array $fields): EntityContracts
    {
        $this->param['fields'] = $fields;
        return $this;
    }

    /**
     * @return EntityContracts
     */
    public function desc(): EntityContracts
    {
        $this->param['ascending'] = 'DESC';
        return $this;
    }

    /**
     * @return EntityContracts
     */
    public function asc(): EntityContracts
    {
        return $this->setParam('ascending', 'ASC');
    }

    /**
     * @return array
     */
    public function delete(): array
    {
        try {

            $query = $this->db->delete($this->getTableName());
            if (isset($this->param['where'])) {
                $query->where($this->param['where']);
            }
            $result = $query->result();
            $this->param = [];
            if ($this->db->error()) {
                throw new Exception($this->db->error());
            }
            return $result;
        } catch (Exception $ex) {
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
                throw new Exception($this->db->error());
            }
            $id = $this->db->lastId();
            if ($id < 1) {
                throw new Exception('no field has been recorded');
            }
            return $result;
        } catch (Exception $ex) {
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
                throw new Exception($query->error());
            }
            return $result;
        } catch (Exception $ex) {
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
     * @param EntityContracts $entity
     * @return object
     */
    protected function hasMany(EntityContracts $entity): object
    {
        return (new HasMany($this, $entity));
    }

    /**
     * @param EntityContracts $entity
     * @return object
     */
    protected function hasOne(EntityContracts $entity): object
    {
        return (new HasOne($this, $entity));
    }
}
