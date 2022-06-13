<?php

namespace SwithFr\SqlQuery;

use DateTime;
use SwithFr\SqlQuery\Traits\HasUpdatedAt;
use SwithFr\SqlQuery\Traits\HasDeletedAt;
use SwithFr\SqlQuery\Contracts\DBInterface;
use function PHPUnit\Framework\objectHasAttribute;

abstract class AbstractRepository
{
    public string $table;

    public string $entityClass;

    public string $pkKey = 'id';

    public function __construct(private DBInterface $db)
    {
    }

    public function update(SqlEntity $entity): bool
    {
        $old = $entity->getOriginal();
        $params = [
            $this->pkKey => $entity->{$this->pkKey},
        ];
        $queryParams = [];

        foreach ($old as $field => $value) {
            if ($entity->$field !== $value) {
                $params[$field] = $entity->$field;
                $queryParams[] = "$field = :$field";
            }
        }

        if (in_array(HasUpdatedAt::class, class_uses($entity), true)) {
            $entity->setUpdatedAt(new DateTime());
        }

        $query = "update {$this->table} set " . implode(',', $queryParams) . " where {$this->pkKey} = :{$this->pkKey}";

        return $this->db->queryStringToStatement($query)->execute($params);
    }

    public function all(): array
    {
        $sql = new SqlQuery($this->db);

        return $sql->query('select {$this->table}.* from {$this->table}')->all();
    }

    public function delete(SqlEntity $entity): bool
    {
        if (in_array(HasDeletedAt::class, class_uses($entity), true)) {
            $entity->setDeletedAt(new DateTime());

            return $this->update($entity);
        }

        $query = "delete from {$this->table} where {$this->pkKey} = :{$this->pkKey}";

        return $this->db->queryStringToStatement($query)->execute([$this->pkKey => $entity->{$this->pkKey}]);
    }
}