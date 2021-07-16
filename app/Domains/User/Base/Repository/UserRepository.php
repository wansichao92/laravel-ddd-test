<?php

namespace App\Domains\User\Base\Repository;

use App\Domains\User\Domain\Models\Interfaces\UserEntityInterface;
use App\Domains\User\Domain\Models\User\CommonUser;
use App\Supports\BaseDoctrineRepository;
use App\Domains\User\Base\Repository\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseDoctrineRepository implements UserRepositoryInterface
{
    public function __construct($class_name = CommonUser::class, $entity_manager = 'user')
    {
        $em = app('registry');

        /* @var $em IlluminateRegistry */
        $this->em = $em;
        $this->entity_manager = $entity_manager;
        $this->class_name = $class_name;

        parent::__construct($em->getManager($entity_manager), $em->getManager($entity_manager)->getClassMetadata($class_name));
    }

    public function create(UserEntityInterface $entity, string $class_name = '')
    {
        if (!empty($class_name) && $this->class_name !== $class_name)
            return $this->reloadRepository($class_name)->create($entity, $class_name);

        $this->getEntityManager()->persist($entity);
    }

    public function getUserDataCountByName(string $name)
    {
        $query = $this->createQueryBuilder('a')->select("count(a.id) as count");

        $or = $query->expr()->andX();
        $or->add($query->expr()->eq('a.name' , "'{$name}'"));

        $num = $query->where($or)->getQuery()->execute();

        return (int)$num[0]['count'];
    }

    public function getUserList(array $where = [], int $limit=10, int $page=1)
    {
        if ($this->class_name !== CommonUser::class)
            return $this->reloadRepository(CommonUser::class)->getUserList($where, $limit, $page);

        $query = $this->createQueryBuilder('a')->select('a');

        if (!empty($where)) {
            $or = $query->expr()->andX();
            if (!empty($where['id']))
                $or->add($query->expr()->eq('a.id', "'{$where['id']}'"));
            if (!empty($where['name']))
                $or->add($query->expr()->eq('a.name', "'{$where['name']}'"));
            if (!empty($where['status']))
                $or->add($query->expr()->eq('a.status', "'{$where['status']}'"));
            if (!empty($or->getParts()))
                $query->where($or);
        }

        $query = $query->orderBy('a.createdAt','desc')->getQuery();

        return $this->paginate($query, $limit, $page);
    }
}
