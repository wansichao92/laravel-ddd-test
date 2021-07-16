<?php

namespace App\Domains\User\Application\Services;

use App\Domains\User\Application\ResponseDtos\URLResponseDto;
use App\Domains\User\Domain\Models\User\CommonUser;
use App\Domains\User\Domain\Service\UserDomainService;
use App\Supports\ClassFactory;
use App\Domains\User\Supports\DomainClass;
use App\Domains\User\Base\Repository\UserRepository;
use App\Domains\User\Job\TestQueue;

class UserAppService
{
    private $_i_user_repository;

    public function __construct(UserRepository $_i_user_repository)
    {
        $this->_i_user_repository = $_i_user_repository;
    }

    public function add($requestDto)
    {
        //ClassFactory::getTestService()->say();

        $checkName = $this->_i_user_repository->getUserDataCountByName($requestDto->name);
        if ($checkName) {
            $errorLog = "姓名{$requestDto->name}已存在，添加数据失败！";
            TestQueue::dispatch($errorLog)->onQueue('test01');
            throw new \Exception($errorLog);
        }

        $entity = (new UserDomainService())->insert($requestDto->toArray());
        $this->_i_user_repository->create($entity);
        $this->_i_user_repository->flush();
        TestQueue::dispatch("姓名{$requestDto->name}添加成功！")->onQueue('test01');
    }

    public function list($requestDto)
    {
        $lists = $this->_i_user_repository->getUserList($requestDto->toArray(), $requestDto->pageSize, $requestDto->page);
        $count = $lists->total();
        foreach ($lists as $key=>$entity) {
            /* @var $entity CommonUser */
            $list[$key] = $entity->toArray();
        }

        return DomainClass::getListResponseDto(compact("count", "list"));
    }

    public function export($requestDto)
    {
        //数据
        $lists = $this->_i_user_repository->getUserList($requestDto->toArray(), $requestDto->pageSize, $requestDto->page);
        if (!empty($lists)) {
            foreach ($lists as $key=>$user) {
                /* @var $user CommonUser */
                $data[$key]['name'] = $user->getName();
                $data[$key]['password'] = $user->getPassword();
                $data[$key]['status'] = $user->getStatus();
            }
        }

        //表头
        $header = ['姓名','密码','状态'];

        $dataMerge = array_merge([$header], $data);

        return $this->exportExcel('人员列表', $dataMerge);
    }

    protected function exportExcel($name, $data, $mergeColumnByFirst = [], $setStyle = [])
    {
        $name = 'excel'.DIRECTORY_SEPARATOR.$name.date('Y-m-d-H-i-s').'.xlsx';
        $result = \Maatwebsite\Excel\Facades\Excel::store(ClassFactory::getBaseExport($data, $mergeColumnByFirst, $setStyle), $name, 'public');

        if (!empty($result))
            return new URLResponseDto(['url'=>config('app.url').'/storage/app/public/'.$name]);

        throw new \Exception('导出失败');
    }

    public function import($requestDto)
    {
        try {
            $rows = (new UserDomainService())->importFileToData($requestDto);
        } catch (\Exception $e) {
            throw new \Exception('上传失败');
        }
        dd($rows);
    }
}
