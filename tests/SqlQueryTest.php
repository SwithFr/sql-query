<?php

use SwithFr\SqlQuery\SqlQuery;
use SwithFr\SqlQuery\PgsqlDatabase;
use SwithFr\Tests\DemoEntities\CategoryDemo;
use SwithFr\Tests\DemoEntities\ProductDemo;
use SwithFr\Tests\DemoEntities\TagDemo;
use SwithFr\Tests\DemoRelations\ProductHaveManyTags;
use SwithFr\Tests\DemoRelations\ProductHaveOneCategory;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

uses()->group('tests');

$db = new PgsqlDatabase();

/**
 * On s'assure qu'on a bien tous les résultats si on fait ->all()
 */
test('SqlQuery returns all results', function () use ($db) {
    $sql = new SqlQuery($db);
    $results = $sql->query('select products.* from products')->all();

    assertNotEmpty($results);
    assertTrue(count($results) > 100);
});

/**
 * On s'assure qu'on a bien tous les résultats si on fait ->all() et que c'est bien mappé avec la bonne classe
 */
test('SqlQuery returns mapped results', function () use ($db) {
    $sql = new SqlQuery($db);
    $results = $sql->query('select products.* from products')->all([], ProductDemo::class);

    foreach ($results as $result) {
        assertInstanceOf(ProductDemo::class, $result);
    }
});

/**
 * On s'assure qu'on a bien un seul résultat si on fait ->one()
 */
test('SqlQuery returns one results', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('select products.* from products')->one();

    assertInstanceOf(stdClass::class, $result);
});

/**
 * On s'assure qu'on a bien un seul résultat si on fait ->one() et que c'est bien mappé avec la bonne classe
 */
test('SqlQuery returns one mapped results', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('select products.* from products')->one([], ProductDemo::class);

    assertInstanceOf(ProductDemo::class, $result);
});

/**
 * On s'assure que les relations "single" sont bien chargées pour un résultat
 */
test('SqlQuery returns related item', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('
            select products.*,  array_to_json(array_agg(c.*)) as _category
            from products
            left join categories c on products.category_id = c.id
            group by products.id
        ')
        ->with(new ProductHaveOneCategory())
        ->one([], ProductDemo::class)
    ;

    assertInstanceOf(ProductDemo::class, $result);
    assertInstanceOf(CategoryDemo::class, $result->getCategory());
});

/**
 * On s'assure que les relations "has many" sont bien chargées pour un résultat
 */
test('SqlQuery returns all related items', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('
            select products.*, array_to_json(array_agg(t.*)) as _tags
            from products
            left join product_tag on product_id = products.id
            left join tags t on tag_id = t.id
            group by products.id
        ')
        ->with(new ProductHaveManyTags())
        ->one([], ProductDemo::class)
    ;

    assertInstanceOf(ProductDemo::class, $result);
    $tags = $result->getTags();
    assertIsArray($tags);

    foreach ($tags as $tag) {
        assertInstanceOf(TagDemo::class, $tag);
    }
});

/**
 * On s'assure que toutes les relations (many et single) sont bien chargées pour tous les résultats
 */
test('SqlQuery returns related items for all items', function () use ($db) {
    $sql = new SqlQuery($db);
    $results = $sql->query('
            select products.*, array_to_json(array_agg(c.*)) as _category, array_to_json(array_agg(t.*)) as _tags from products
            left join categories c on products.category_id = c.id
            left join product_tag on product_id = products.id
            left join tags t on tag_id = t.id
            group by products.id
        ')
        ->withs([
            new ProductHaveOneCategory(),
            new ProductHaveManyTags(),
        ])
        ->all([], ProductDemo::class)
    ;

    assertIsArray($results);
    assertTrue(count($results) > 100);

    foreach ($results as $result) {
        assertInstanceOf(ProductDemo::class, $result);
        assertInstanceOf(CategoryDemo::class, $result->getCategory());

        foreach ($result->getTags() as $tag) {
            assertInstanceOf(TagDemo::class, $tag);
        }
    }
});

test('Return null if not found', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('select products.* from products where id = 999999999999999999999999')->one();

    assertNull($result);
});

test('Return empty array if no related', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('
            select products.*, array_to_json(array_agg(t.*)) as _tags
            from products
            left join product_tag on product_id = products.id
            left join tags t on tag_id = t.id
            where products.id = 42
            group by products.id
        ')
        ->with(new ProductHaveManyTags())
        ->one([], ProductDemo::class)
    ;

    assertNotNull($result);
    assertIsArray($result->getTags());
    assertEmpty($result->getTags());
});

/**
 * Si on ne donne pas de related_class il faut que dans notre entity on ait une propriété
 * typée avec stdClass ou object.
 */
test('Related are stdclass if there is no related_class', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('
            select products.*,  array_to_json(array_agg(c.*)) as _cat
            from products
            left join categories c on products.category_id = c.id
            group by products.id
        ')
        ->with(new ProductHaveOneCategory('cat', '_cat'))
        ->one([], ProductDemo::class)
    ;

    assertNotNull($result);
    assertInstanceOf(stdClass::class, $result->getCat());
});

test('Related are stdclass if there is no related_class many relation', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('
            select products.*, array_to_json(array_agg(t.*)) as _tagsstdclass
            from products
            left join product_tag on product_id = products.id
            left join tags t on tag_id = t.id
            where products.id = 42
            group by products.id
        ')
        ->with(new ProductHaveManyTags('tagsstdclass', '_tagsstdclass'))
        ->one([], ProductDemo::class)
    ;

    assertNotNull($result);
    foreach ($result->getTagsStdClass() as $tag) {
        assertInstanceOf(stdClass::class, $tag);
    }
});
