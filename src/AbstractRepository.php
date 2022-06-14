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

    private SqlQuery $sqlQuery;

    public function __construct(private DBInterface $db)
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

    /**
     * @param T $entity
     * @return T
     */
    public function insert(SqlEntity $entity)
    {
        $hasId = method_exists($entity, 'setId');
        $arrayEntity = $entity->toArray();
        if ($hasId) {
            unset($arrayEntity['id']);
        }
        $fields = array_keys($arrayEntity);
        $columns = implode(',', $fields);
        $values = implode(',', array_map(static fn ($f) => ":$f", $fields));
        $query = "insert into {$this->table} ($columns) values ($values)";

        $record = $this->db->getPdo()->prepare($query)->execute($arrayEntity);

        if ($hasId) {
            /** @phpstan-ignore-next-line */
            $entity->setId($this->db->getPdo()->lastInsertId());
        }

        return $entity;
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

    public function delete(SqlEntity $entity): bool
    {
        if (method_exists($entity, 'setDeletedAt')) {
            $entity->setDeletedAt(new DateTime());

            return $this->update($entity);
        }

        $query = "delete from {$this->table} {$this->_buildPKWhereClause()}";

        return $this->db->queryStringToStatement($query)->execute($this->_getPkParam($entity));
    }

    private function _buildPKWhereClause(): string
    {
        return " where {$this->pkKey} = :{$this->pkKey}";
    }

    private function _getPkParam(SqlEntity|int $entity): array
    {
        $id = $entity instanceof SqlEntity ? $entity->{$this->pkKey} : $entity;
        return [$this->pkKey => $id];
    }
}