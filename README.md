# SQL QUERY

L'idée est d'avoir une classe pour utiliser des requêtes SQL "natives" sans passer par un ORM et être capable d'avoir
simplement des relations.
Donc pouvoir dire que pour une requête donnée, je veux mapper les résultats avec une classe donnée. Et avoir les
relations chargées également dans des classes spécifiques.
En plus de ça, comme on précise à chaque fois quelle classe doit être utilisée on peut imaginer en avoir plusieurs pour
un même jeu de données qui serait spécifique au traitement voulu.
Par exemple avoir une entité AdminProduct, FrontProduct, CartProduct...

## Utilisation

```php
$sql = new SqlQuery($db);
$result = $sql->query('
        select products.*, array_to_json(array_agg(c.*)) as _category, array_to_json(array_agg(c.*)) as _category_user, array_to_json(array_agg(t.*)) as _tags
        from products
        left join categories c on products.category_id = c.id
        left join users u on c.user_id = u.id
        left join product_tag on product_id = products.id
        left join tags t on tag_id = t.id
        where c.id in (1, 2)
        group by products.id
    ')
    ->with('category', [
        'related_class' => CategoryDemo::class,
    ])
    ->with('category.user', [
        'related_class' => UserDemo::class,
    ])
    ->with('tags', [
        'related_class' => TagDemo::class,
        'has_many' => true,
    ])
    ->one([], ProductDemo::class)
;
```
## Documentation de `SqlQuery`

L'astuce est d'agréger les infos liées au format json via `array_to_json(array_agg(table_liée.*)) as _nom_relation`;
Pour ce faire il est important de faire un `group by item_principal.id` et un `select item_principal.*`.

| Method                                      | Explanations                                                                                                                              |
|---------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------|
| one(array $params, string $castInto = null) | Récupère un seul résultat. `$params` = tableau des variables a passer a la query. `$castInto` = nom de la classe pour mapper le résultat. |
| all(array $params, string $castInto = null) | Récupère tous les résultats. Mêmes arguments que `one()`                                                                                  |
| with(string $relation, array $params = [])  | Ajoute une relation à charger. Voir [Documentation relations](#documentation-relations)                                                                      |
| withs(array $withs)                         | Ajoute plusieurs relations en une fois.                                                                                                   |

## Documentation relations

| Name                 | Default                            | Explanations                                                |
|----------------------|------------------------------------|-------------------------------------------------------------|
| related_class        | `sdtClass::class`                  | Nom de la classe avec laquelle seront mappés les items liés |
| has_many             | `false`                            | Est-ce que la relation est un tableau ou non ?              |
| query_aggregated_key | nom de la relation avec "_" devant | La clé utilisée dans la query pour agréger les infos liées. |

### Getting started

IL faut avoir `docker`, `composer`, ainsi que `make`  d'installés.

- `git clone git@github.com:SwithFr/sql-query.git`
- `make install`
- `make start` et `make stop` pour lancer/stopper le conteneur PostgreSQL.
- `make migrate`
- `make seed`

### Commandes make

- `make tests` pour lancer les tous tests.
- `make test-advanced` pour lancer les tests sur les relations imbriquées.
- `make cs` pour fixer le code style.
- `make analyse` pour lancer l'analyse statique du code.

### Todo

- [ ] Trouver une meilleure interface pour `DBInterface` ?
- [ ] Mettre en place la CI
- [ ] Faire du lazy loading... par certain que ce soit à cette classe de gérer ça.
- [ ] Refacto pour des "SqlEntity".
- [ ] Gerer les relations via des classes.