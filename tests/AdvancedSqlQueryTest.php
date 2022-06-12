<?php

use SwithFr\Tests\PgsqlDB;
use SwithFr\Tests\DemoEntities\UserDemo;
use SwithFr\SqlQuery\SqlQuery;
use SwithFr\Tests\DemoEntities\ProductDemo;
use SwithFr\Tests\DemoEntities\CategoryDemo;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertInstanceOf;

$db = new PgsqlDB();

uses()->group('tests', 'advanced');

test('Test simple nested', function () use ($db) {
    $sql = new SqlQuery($db);
    $result = $sql->query('
            select products.*,  array_to_json(array_agg(c.*)) as _category, array_to_json(array_agg(c.*)) as _category_user
            from products
            left join categories c on products.category_id = c.id
            left join users u on c.user_id = u.id
            where c.id in (1, 2)
            group by products.id
        ')
        ->with('category', [
            'related_class' => CategoryDemo::class,
        ])
        ->with('category.user', [
            'related_class' => UserDemo::class,
        ])
        ->one([], ProductDemo::class)
    ;

    assertInstanceOf(UserDemo::class, $result->getCategory()->getUser());
});

test('Test multi nested', function () use ($db) {
    $sql = new SqlQuery($db);
    $results = $sql->query('
            select products.*,  array_to_json(array_agg(c.*)) as _category, array_to_json(array_agg(c.*)) as _category_user, array_to_json(array_agg(pu.*)) as _user_products
            from products
            left join categories c on products.category_id = c.id
            left join users u on c.user_id = u.id
            left join products pu on pu.user_id = u.id
            where c.id in (1, 2)
            group by products.id
        ')
        ->withs([
            'category' => [
                'related_class' => CategoryDemo::class,
            ],
            'category.user' => [
                'related_class' => UserDemo::class,
            ],
            'category.user.products' => [
                'related_class' => ProductDemo::class,
                'has_many' => true,
                'query_aggregated_key' => '_user_products',
            ],
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