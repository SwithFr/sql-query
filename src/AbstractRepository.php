<?php

namespace SwithFr\SqlQuery;

use DateTime;
use SwithFr\SqlQuery\Traits\HasUpdatedAt;
use SwithFr\SqlQuery\Traits\HasDeletedAt;
use SwithFr\SqlQuery\Contracts\DBInterface;
use SwithFr\SqlQuery\Exceptions\RecordNotFoundException;
use function PHPUnit\Framework\objectHasAttribute;

/**
 * @template T of \SwithFr\SqlQuery\SqlEntity
 */
abstract class AbstractRepository
{
    public string $table;

    /**
     * @var class-string<T>
     */
    public string $entityClass;

    public string $pkKey = 'id';

    protected SqlQuery $sqlQuery;

    /**
     * @var \SwithFr\SqlQuery\Contracts\RelationshipInterface[]
     */
    private array $_withs = [];

    public function __construct(private readonly DBInterface $db)
    {
        $this->sqlQuery = new SqlQuery($db);
    }

    /**
     * @param int $id
     *
     * @return T
     * @throws \SwithFr\SqlQuery\Exceptions\RecordNotFoundException
     */
    public function getById(int $id)
    {
        $record = $this->sqlQuery
            ->query("select {$this->table}.* from {$this->table} {$this->_buildPKWhereClause()} ")
            ->one($this->_getPkParam($id), $this->entityClass)
        ;

        if ($record === null) {
            throw new RecordNotFoundException();
        }

        return $record;
    }

    public function withs(array $relations): static
    {
        $this->_withs = $relations;

        return $this;
    }

    /**
     * @param T $entity
     * @return T
     */
    public function insert(SqlEntity $entity)
    {
        $hasId = method_exists($entity, 'setId');
        ['fields' => $fields, 'columns' => $columns, 'arrayEntity' => $arrayEntity, 'values' => $values] = $this->_getInsertColumns($entity, $hasId);
        $query = "insert into {$this->table} ($columns) values ($values)";

        $record = $this->db->getPdo()->prepare($query)->execute($arrayEntity);

        if ($hasId) {
            $entity->setId($this->db->getPdo()->lastInsertId());
        }

        return $entity;
    }

    /**
     * @param T[] $entities
     * @return T[]
     */
    public function insertAll(array $entities): array
    {
        $first = $entities[0];
        $hasId = method_exists($first, 'setId');
        $return = $hasId ? "returning id" : '';
        ['fields' => $fields, 'columns' => $columns, 'arrayEntity' => $arrayEntity, 'values' => $values] = $this->_getInsertColumns($first, $hasId);
        $allValues = implode(',', array_fill(0, count($entities), "($values)"));

        $query = "insert into {$this->table} ($columns) values $allValues $return";

        $statement =  $this->db->getPdo()->prepare($query);
        $statement->execute($arrayEntity);

        if ($hasId) {
            $ids = $statement->fetchAll();
            foreach ($entities as $entity) {
                $entity->setId($this->db->getPdo()->lastInsertId());
            }
        }

        return $entities;
    }

    public function update(SqlEntity $entity): bool
    {
        $old = $entity->getOriginal();
        $params = $this->_getPkParam($entity);
        $queryParams = [];

        foreach ($old as $field => $value) {
            if ($entity->$field !== $value) {
                $params[$field] = $entity->$field;
                $queryParams[] = "$field = :$field";
            }
        }

        if (method_exists($entity, 'setUpdatedAt')) {
            $entity->setUpdatedAt(new DateTime());
        }

        $query = "update {$this->table} set " . implode(',', $queryParams) . $this->_buildPKWhereClause();

        return $this->db->queryStringToStatement($query)->execute($params);
    }

    public function all(): array
    {
        return $this->sqlQuery->query("select {$this->table}.* from {$this->table}")->all([], $this->entityClass);
    }

    public function query(string $query): SqlQuery
    {
        return $this->sqlQuery->query($query);
    }

    public function delete(SqlEntity $entity): bool
    {
        if (method_exists($entity, 'setDeletedAt')) {
            $entity->setDeletedAt(new DateTime());

            return $this->update($entity);
        }

        $query = "delete from {$this->table} {$this->_buildPKWhereClause()}";

        return $this->db->queryStringToStatement($query)->execute($this->_getPkParam($entity));
    }

    public function deleteAll(array $entities): void
    {
        $ids = [];

        foreach ($entities as $entity) {
            if ($entity instanceof SqlEntity && method_exists($entity, 'getId')) {
                $ids[] = $entity->getId();
            } else if (is_int($entity)) {
                $ids[] = $entity;
            }
        }

        $query = "delete from {$this->table} {$this->_buildPKWhereInClause(count($entities))}";

        $this->db->queryStringToStatement($query)->execute($ids);
    }

    private function _buildPKWhereClause(): string
    {
        return " where {$this->pkKey} = :{$this->pkKey}";
    }

    private function _buildPKWhereInClause(int $count): string
    {
        $placeholders = implode(',', array_fill(0, $count, '?'));
        return " where {$this->pkKey} in ($placeholders)";
    }

    private function _getPkParam(SqlEntity|int $entity): array
    {
        $id = $entity instanceof SqlEntity ? $entity->{$this->pkKey} : $entity;
        return [$this->pkKey => $id];
    }

    /**
     * @param T $entity
     * @param bool $hasId
     *
     * @return array
     */
    private function _getInsertColumns(SqlEntity $entity, bool $hasId): array
    {
        $arrayEntity = $entity->toArray();

        if ($hasId) {
            unset($arrayEntity['id']);
        }

        $fields = array_keys($arrayEntity);

        return [
            'fields' => $fields,
            'columns' => implode(',', $fields),
            'values' => implode(',', array_map(static fn ($f) => ":$f", $fields)),
            'arrayEntity' => $arrayEntity,
        ];
    }
}