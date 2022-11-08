<?php

namespace SwithFr\Tests\DemoRepositories;

use SwithFr\SqlQuery\AbstractRepository;
use SwithFr\Tests\DemoEntities\ProductDemo;

class ProductsRepository extends AbstractRepository
{
    public string $table = 'products';

    public string $entityClass = ProductDemo::class;

    public function allWithCategory(): array
    {
        return $this->sqlQuery->query('
            select products.*, array_to_json(array_agg(c.*)) as _category
            from products
            left join categories c on products.category_id = c.id
            group by products.id
        ')->with(ProductDemo::relatedCategory())->all([], $this->entityClass);
    }
}