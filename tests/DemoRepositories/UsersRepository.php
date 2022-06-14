<?php

namespace SwithFr\Tests\DemoRepositories;

use SwithFr\SqlQuery\AbstractRepository;
use SwithFr\Tests\DemoEntities\UserDemo;

class UsersRepository extends AbstractRepository
{
    public string $table = 'users';

    public string $entityClass = UserDemo::class;
}