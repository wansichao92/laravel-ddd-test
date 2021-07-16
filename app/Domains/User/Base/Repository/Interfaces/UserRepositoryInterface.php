<?php

namespace App\Domains\User\Base\Repository\Interfaces;

use App\Domains\User\Domain\Models\Interfaces\UserEntityInterface;

interface UserRepositoryInterface
{
    public function create(UserEntityInterface $entity, string $class_name = '');

    public function getUserDataCountByName(string $name);

    public function getUserList(array $where = [], int $limit=10, int $page=1);
}
