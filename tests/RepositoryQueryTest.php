<?php

use Faker\Factory;
use SwithFr\SqlQuery\SqlQuery;
use SwithFr\SqlQuery\PgsqlDatabase;
use SwithFr\Tests\DemoEntities\UserDemo;
use SwithFr\Tests\DemoEntities\ProductDemo;
use SwithFr\Tests\DemoEntities\CategoryDemo;
use SwithFr\Tests\DemoRepositories\UsersRepository;
use SwithFr\Tests\DemoRepositories\ProductsRepository;
use SwithFr\SqlQuery\Exceptions\RecordNotFoundException;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertInstanceOf;

uses()->group('tests');

$db = new PgsqlDatabase();

test('Test update query', function () use ($db) {
    $repo = new UsersRepository($db);
    $sql = new SqlQuery($db);

    /** @var UserDemo $user */
    $user = $sql->query('select * from users where id = 1')->one([], UserDemo::class);
    $old = $user->getName();

    $new = 'Tony Stark '.time();
    $user->setName($new);

    $repo->update($user);

    $user = $sql->query('select * from users where id = 1')->one([], UserDemo::class);

    assertNotEquals($old, $user?->getName());
    assertEquals($new, $user?->getName());
});

test('Test delete query', function () use ($db) {
    $repo = new UsersRepository($db);
    $sql = new SqlQuery($db);

    $name = time();
    $db->queryStringToStatement('insert into users (name) values (:name)')->execute(['name' => $name]);

    /** @var UserDemo $user */
    $user = $sql->query('select * from users where name = :name')->one(['name' => $name], UserDemo::class);

    $repo->delete($user);

    $user = $sql->query('select * from users where name = :name')->one(['name' => $name], UserDemo::class);

    assertNull($user);
});

test('Test delete query just set deleted_at', function () use ($db) {
    $repo = new ProductsRepository($db);
    $sql = new SqlQuery($db);

    /** @var ProductDemo $product */
    $product = $sql->query('select * from products where id = 1')->one([], ProductDemo::class);

    $repo->delete($product);

    /** @var ProductDemo $product */
    $product = $sql->query('select * from products where id = 1')->one([], ProductDemo::class);

    assertNotNull($product);
    assertInstanceOf(DateTime::class, $product->getDeletedAt());
});

test('Test get all', function () use ($db) {
    $repo = new ProductsRepository($db);

    $products = $repo->all();

    assertNotEmpty($products);
});

test('Getting by ID', function () use ($db) {
    $repo = new ProductsRepository($db);

    $product = $repo->getById(1);

    assertNotEmpty($product);
    assertInstanceOf(ProductDemo::class, $product);
});

test('Getting by ID throw not found', function () use ($db) {
    $repo = new ProductsRepository($db);

    $product = $repo->getById(999999999);
})->throws(RecordNotFoundException::class);

test('Test insert query', function () use ($db) {
    $repo = new ProductsRepository($db);
    $product = new ProductDemo([
        'name' => 'Mon super produit',
    ]);

    assertNull($product->getId());

    $product = $repo->insert($product);

    assertNotNull($product->getId());
});

test('Test insert query multiple', function () use ($db) {
    $repo = new ProductsRepository($db);
    $faker = Factory::create();
    $products = [];
    for ($i = 0; $i < 10; $i++) {
        $products[] = new ProductDemo([
            'name' => $faker->name,
        ]);
    }

    $products = $repo->insertAll($products);

    assertIsArray($products);
    foreach ($products as $product) {
        assertNotNull($product->getId());
    }
});

test('Test delete multiple', function () use ($db) {
    $repo = new ProductsRepository($db);
    $faker = Factory::create();
    $products = [];
    for ($i = 0; $i < 10; $i++) {
        $products[] = new ProductDemo([
            'name' => $faker->name,
        ]);
    }

    $products = $repo->insertAll($products);
    $ids = implode(',', array_map(fn($p) => $p->getId(), $products));
    $repo->deleteAll($products);

    $products = $repo->query("select products.* from products where id in ($ids)")->all();

    assertEmpty($products);
});

test('Test delete multiple with entities and ids', function () use ($db) {
    $repo = new ProductsRepository($db);
    $faker = Factory::create();
    $products = [];
    for ($i = 0; $i < 10; $i++) {
        $products[] = new ProductDemo([
            'name' => $faker->name,
        ]);
    }

    $products = $repo->insertAll($products);
    $ids = array_map(static fn($p) => $p->getId(), $products);
    $products[0] = $ids[0]; // on remplace une entité par un id
    $ids = implode(',', $ids);
    $repo->deleteAll($products);

    $products = $repo->query("select products.* from products where id in ($ids)")->all();

    assertEmpty($products);
});

test('Test get all with related', function () use ($db) {
    $repo = new ProductsRepository($db);

    $products = $repo->allWithCategory();

    assertNotEmpty($products);

    foreach ($products as $product) {
        assertInstanceOf(CategoryDemo::class, $product->getCategory());
    }
});