<?php

use SwithFr\SqlQuery\SqlQuery;
use SwithFr\SqlQuery\PgsqlDatabase;
use SwithFr\Tests\DemoEntities\ProductDemo;
use SwithFr\Tests\DemoEntities\UserDemo;
use SwithFr\Tests\DemoRelations\CategoryBelongsToOneUser;
use SwithFr\Tests\DemoRelations\ProductHaveOneCategory;
use SwithFr\Tests\DemoRelations\UserHaveManyProducts;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;

$db = new PgsqlDatabase();

uses()->group('tests', 'advanced');

test('Test simple nested', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('
            select products.*,  array_to_json(array_agg(c.*)) as _category, array_to_json(array_agg(u.*)) as _category_user
            from products
            left join categories c on products.category_id = c.id
            left join users u on c.user_id = u.id
            where c.id in (1, 2)
            group by products.id
        ')
        ->with(new ProductHaveOneCategory())
        ->with(new CategoryBelongsToOneUser())
        ->one([], ProductDemo::class)
    ;

    assertInstanceOf(UserDemo::class, $result->getCategory()->getUser());
});

test('Test multi nested', function () use ($db) {
    $sql = new SqlQuery($db);
    $results = $sql->query('
            select products.*,  array_to_json(array_agg(c.*)) as _category, array_to_json(array_agg(u.*)) as _category_user, array_to_json(array_agg(pu.*)) as _user_products
            from products
            left join categories c on products.category_id = c.id
            left join users u on c.user_id = u.id
            left join products pu on pu.user_id = u.id
            where c.id in (1, 2)
            group by products.id
        ')
        ->withs([
            new ProductHaveOneCategory(),
            new CategoryBelongsToOneUser(),
            new UserHaveManyProducts('category.user.products'),
        ])
        ->all([], ProductDemo::class)
    ;

    foreach ($results as $result) {
        assertInstanceOf(UserDemo::class, $result->getCategory()->getUser());
        assertIsArray($result->getCategory()->getUser()->getProducts());

        foreach ($result->getCategory()->getUser()->getProducts() as $product) {
            assertInstanceOf(ProductDemo::class, $product);
        }
    }
});