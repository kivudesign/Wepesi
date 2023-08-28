<?php

namespace Wepesi\Core\BaseEntityModel;

use ReflectionClass;
use ReflectionProperty;
use Wepesi\Core\Orm\DB;
use Wepesi\Core\Orm\Relations\HasMany;
use Wepesi\Core\Orm\WhereQueryBuilder\WhereBuilder;

/**
 *
 */
abstract class Entity
{
    /**
     * @var DB
     */
    private DB $db;
    /**
     * @var array
     */
    private array $include_entity;
    use EntityReflexion;

    /**
     *
     */
    public function __construct()
    {
        $this->db = DB::getInstance();
        $this->include_entity = [];
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
        return trim($this->getName()) !== '' ? trim($this->getName()) : $this->getClassDefinition()->table;
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return '';
    }

    /**
     * @param Entity|null $entity
     * @return object|array
     */
    private function getClassDefinition(?Entity $entity = null): object
    {
        try {
            $class_entity = $entity ?? $this;
            $class = new ReflectionClass($class_entity);
            if (!$class->isInstance($class_entity)) {
                throw new \Exception('Entity model undefined');
            }
            $class_name = $class->getShortName();
            $table_name = strtolower($class->getShortName());
            $table_field = [];
            foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyName = $property->getName();
                $table_field[] = $propertyName;
            }
            return (object)[
                'table' => $table_name,
                'fields' => $table_field,
                'class' => $class_name
            ];
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage()];
        }
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
     * @param Entity $entityName
     * @param bool $inner
     * @return $this
     */
    public function include(Entity $entityName, bool $inner = false): Entity
    {
        try {
            $entity_table_name = $this->getEntityName($entityName)['table'];
            if (isset($entity_table_name['exception'])) {
                throw new \Exception($entity_table_name['exception']);
            }
            $entity_object = $this->getEntityRelationLink($entity_table_name);
            $relation = [
                'entity' => $entity_object ?? $entity_table_name,
                'table' => $entity_table_name,
                'join' => $inner ? 'INNER' : 'LEFT',
            ];
            if (is_array($entity_object) && isset($entity_object['exception'])) {
                throw new \Exception($entity_object['exception']);
            } else {
                $entity_relation = $entity_object->getRelation();
                $primary_key = $this->getTableName() . '.' . $entity_relation->primary_key;
                $foreign_key = $entity_table_name . '.' . $entity_relation->foreign_key;
                $relation['on'] = ' ON ' . $primary_key . '=' . $foreign_key;
            }
            $this->include_entity [] = $relation;
        } catch (\Exception $ex) {
            print_r(['exception' => $ex->getMessage()]);
        }
        return $this;
    }


    /**
     * @param string $entity_name
     * @return array|mixed
     */
    private function getEntityRelationLink(string $entity_name)
    {
        try {
            $class = new ReflectionClass($this);
            $object = $class->newInstance();
            $method = $class->getMethod(strtolower($entity_name));
            $method->setAccessible(true);
            return $method->invoke($object);
        } catch (\Exception $ex) {
            return ['exception' => $ex->getMessage() . ' from class ' . $this->getClassDefinition()->class];
        }
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
     * @param Entity $entity
     * @return object|HasMany
     */
    protected function hasMany(Entity $entity): object
    {
        return (new HasMany($this, $entity));
    }
}