<?php

use SwithFr\SqlQuery\PgsqlDatabase;
use function PHPUnit\Framework\assertTrue;

$db = new PgsqlDatabase();

it('migrate', function() use ($db) {
    // Drop
    $db->getPdo()->query('drop table if exists products;');
    $db->getPdo()->query('drop table if exists categories;');
    $db->getPdo()->query('drop table if exists tags;');
    $db->getPdo()->query('drop table if exists product_tag;');
    $db->getPdo()->query('drop table if exists users;');

    // Migrate
    $db->getPdo()->query('
        create table products
        (
            id serial
                constraint products_pk
                    primary key,
            name        varchar not null,
            category_id integer,
            user_id     integer,
            deleted_at timestamp default null
        );
    ');
    $db->getPdo()->query('create unique index products_id_uindex on products (id);');
    $db->getPdo()->query('
        create table categories
        (
            id      serial
                constraint categories_pk
                    primary key,
            name    varchar not null,
            user_id integer
        );
    ');
    $db->getPdo()->query('create unique index categories_id_uindex on categories (id);');
    $db->getPdo()->query('
        create table tags
        (
            id   serial
                constraint tags_pk
                    primary key,
            name varchar not null
        );
    ');
    $db->getPdo()->query('create unique index tags_id_uindex on tags (id);');
    $db->getPdo()->query('
        create table product_tag
        (
            product_id integer not null,
            tag_id     integer not null
        );
    ');
    $db->getPdo()->query('
        create table users
        (
            id   serial
                constraint users_pk
                    primary key,
            name varchar not null,
        updated_at timestamp default current_timestamp
        );
    ');
    $db->getPdo()->query('create unique index users_id_uindex on users (id);');

    assertTrue(true);
})->group('migrate');

it('seed', function() use ($db) {
    $faker = Faker\Factory::create();

    $db->getPdo()->beginTransaction(); // also helps speed up your inserts.

    // On truncate d'abord
    $db->getPdo()->query('truncate table products restart identity;');
    $db->getPdo()->query('truncate table categories restart identity;');
    $db->getPdo()->query('truncate table tags restart identity;');
    $db->getPdo()->query('truncate table product_tag restart identity;');
    $db->getPdo()->query('truncate table users restart identity;');

    for ($i = 0; $i < 10000; $i++) {
        $q = $db->getPdo()->prepare("insert into products (name, category_id, user_id) values (:name, :category, :user)");
        $user = $i < 10 ? 1 : null;
        $q->execute([$faker->name, random_int(1, 1000), $user]);
    }

    for ($i = 0; $i < 1000; $i++) {
        $q = $db->getPdo()->prepare("insert into categories (name) values (:name)");
        $q->execute([$faker->name]);
    }

    for ($i = 0; $i < 1000; $i++) {
        $q = $db->getPdo()->prepare("insert into tags (name) values (:name)");
        $q->execute([$faker->name]);
    }

    for ($i = 0; $i < 10000; $i++) {
        $q = $db->getPdo()->prepare("insert into product_tag (product_id, tag_id) values (:p, :t)");
        $q->execute([random_int(1, 1000), random_int(1, 1000)]);
    }

    // On supprime tous les tags liés au product id = 42 pour les tests
    $db->getPdo()->query("delete from product_tag where product_id = 42");

    $q = $db->getPdo()->prepare("insert into users (name) values (:name)");
    $q->execute([$faker->name]);

    $db->getPdo()->query("update categories set user_id = 1 where id = 1");

    $db->getPdo()->commit();

    assertTrue(true);
})->group('seed');