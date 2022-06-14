<?php

namespace SwithFr\SqlQuery;

use PDO;
use PDOStatement;
use stdClass;
use SwithFr\SqlQuery\Contracts\DBInterface;
use SwithFr\SqlQuery\Contracts\RelationshipInterface;

class SqlQuery
{
    private string $_query;

    private array $_withs;

    public function __construct(private readonly DBInterface $db)
    {
    }

    public function query(string $query): self
    {
        $this->_withs = [];
        $this->_query = $query;

        return $this;
    }

    /**
     * @template T
     * @param array $params
     * @param class-string<T>|null $castInto
     *
     * @return stdClass[]|T[]
     */
    public function all(array $params = [], string $castInto = null): array
    {
        $statement = $this->db->queryStringToStatement($this->_query);
        $statement->execute($params);

        $this->_setFetchIntoClass($statement, $castInto);

        $items = $statement->fetchAll();

        if (empty($items)) {
            return [];
        }

        return $this->_loadRelated($items);
    }

    /**
     * @template T
     * @param array $params
     * @param class-string<T>|null $castInto
     *
     * @return T|null
     */
    public function one(array $params = [], string $castInto = null)
    {
        $statement = $this->db->queryStringToStatement($this->_query);
        $statement->execute($params);

        $this->_setFetchIntoClass($statement, $castInto);

        $item = $statement->fetch();

        $item = $this->_loadRelated([$item])[0] ?? null;

        return $item ?: null;
    }

    /**
     * @param RelationshipInterface[] $withs
     */
    public function withs(array $withs): self
    {
        foreach ($withs as $relation) {
            $this->with($relation);
        }

        return $this;
    }

    public function with(RelationshipInterface $relation): self
    {
        $this->_withs[$relation->getName()] = [
            'query_aggregated_key' => $relation->getQueryAggregatedKey(), // La clé utilisée dans la query pour "agréger" les infos liées
            'related_class' => $relation->getRelatedClassName() ?? stdClass::class, // La classe à utiliser pour mapper les items liés
            'has_many' => $relation->hasMany(), // La relation est un array ou non ?
        ];

        return $this;
    }

    private function _setFetchIntoClass(PDOStatement $statement, string $castInto = null): void
    {
        if ($castInto !== null) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $castInto);
        }
    }

    private function _loadRelated(array $items): array
    {
        // Si on a des relations à charger
        if (! empty($this->_withs)) {
            foreach ($items as $item) {
                foreach ($this->_withs as $relation => $params) {
                    $queryAggregatedKey = $params['query_aggregated_key'];
                    try {
                        // On transforme les items liés (en json via la query) en tableau php
                        /** @var array $relatedItems */
                        $relatedItems = json_decode($item->{$queryAggregatedKey}, true, 512, JSON_THROW_ON_ERROR);
                        // On ajoute à l'item la relation
                        $item = $this->_setRelation($item, $relation, $params, $relatedItems);
                    } catch (\Exception $e) {
                        dd($e);
                    }
                    // Enfin on supprime la version "raw" des éléments liés
                    unset($item->{$queryAggregatedKey});
                }
            }
        }

        return $items;
    }

    /**
     * Converti les items liés en objet php selon la classe passée en param
     */
    private function _buildRelatedItems(array $relatedItems, string $relatedClass): array
    {
        return array_filter(array_map(static fn($r) => $r !== null ? new $relatedClass($r) : null, $relatedItems));
    }

    private function _setRelation(object $item, string $relation, array $params, array $relatedItems): object
    {
        $relatedClass = $params['related_class'];
        $nestedRelations = explode('.', $relation);

        // Si on est sur une relation imbriquée
        if (count($nestedRelations) > 1) {
            $i = $item;
            foreach ($nestedRelations as $k => $nestedRelation) {
                // Si la relation est déjà chargée on passe à la suivante
                if (isset($i->{$nestedRelation})) {
                    $i = $i->{$nestedRelation};
                    continue;
                }

                // On ajoute la relation
                $this->_setRelation($i, $nestedRelation, $params, $relatedItems);
            }
        } elseif ($params['has_many']) {
            $item->{$relation} = $this->_buildRelatedItems($relatedItems, $relatedClass);
        } else {
            $item->{$relation} = new $relatedClass($relatedItems[0] ?? []);
        }

        return $item;
    }
}