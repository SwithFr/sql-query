<?php

use SwithFr\SqlQuery\SqlQuery;
use SwithFr\SqlQuery\PgsqlDatabase;
use SwithFr\Tests\DemoEntities\UserDemo;
use SwithFr\Tests\DemoEntities\ProductDemo;
use SwithFr\Tests\DemoRepositories\UsersRepository;
use SwithFr\Tests\DemoRepositories\ProductsRepository;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
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

    $product = $sql->query('select * from products where id = 1')->one([], ProductDemo::class);

    assertNotNull($product);
    assertInstanceOf(DateTime::class, $product->getDeletedAt());
});