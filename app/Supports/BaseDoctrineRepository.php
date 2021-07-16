<?php

namespace App\Supports;

use App\Supports\Interfaces\BaseDotrineInterface;
use Doctrine\ORM\EntityRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class BaseDoctrineRepository extends EntityRepository implements BaseDotrineInterface
{

    use PaginatesFromParams;

    protected $em;

    protected $entity_manager;

    protected $class_name;

    /**
     * 通过反射创建数据库实体类
     * @return Object
     * @throws \ReflectionException
     */
    public function createEntity() {
        $reflect = new \ReflectionClass($this->getEntityName());
        return $reflect->newInstanceArgs();
    }

    /**
     * 重新创建仓储
     * @param string $class_name 需要创建的类名
     * @param string $entity_manager 实体类管理器名称
     * @return BaseDoctrineRepository
     */
    protected function reloadRepository(string $class_name, $entity_manager = ''): BaseDoctrineRepository
    {
        if (empty($entity_manager) && empty($this->entity_manager)) {
            return $this;
        }
        if (empty($entity_manager)) {
            $entity_manager = $this->entity_manager;
        }
        return new $this($class_name, $entity_manager);
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    public function beginTransaction()
    {
        $this->getEntityManager()->beginTransaction();
    }

    public function rollback()
    {
        $this->getEntityManager()->getConnection()->rollBack();
    }

    public function commit()
    {
        $this->getEntityManager()->getConnection()->commit();
    }

    public function startLog()
    {
        $logger = new \Doctrine\DBAL\Logging\DebugStack();
        $this->getEntityManager()->getConnection()->getConfiguration()->setSQLLogger($logger);
        return $logger;
    }

    public function getEntityChangeSet($entity){
        $uow = $this->getEntityManager()->getUnitOfWork();
        $uow->computeChangeSets();
        $changeList = $uow->getEntityChangeSet($entity);

        $data = [];
        foreach ($changeList as $key => $value){
            if($value[0] != $value[1]){
                $data[$key] = $value;
            }
        }
        return $data;
    }
}
