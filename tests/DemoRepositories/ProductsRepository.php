<?php

namespace SwithFr\Tests\DemoRepositories;

use SwithFr\SqlQuery\AbstractRepository;
use SwithFr\Tests\DemoEntities\ProductDemo;

class ProductsRepository extends AbstractRepository
{
    public string $table = 'products';

    public string $entityClass = ProductDemo::class;
}