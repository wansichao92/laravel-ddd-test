<?php

namespace App\Domains\User\Application\Services;

use App\Domains\Product\Domain\Service\ProductLogService;
use App\Domains\User\Application\ResponseDtos\URLResponseDto;
use App\Domains\User\Domain\Models\User\CommonUser;
use App\Domains\User\Domain\Service\UserDomainService;
use App\Jobs\CommonUserPush;
use App\Services\ElasticsearchService;
use App\Services\HelpService;
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
        $checkName = $this->_i_user_repository->getUserDataCountByName($requestDto->name);
        if ($checkName) {
            throw new \Exception("姓名{$requestDto->name}已存在，添加数据失败！");
        }

        $this->_i_user_repository->beginTransaction();

        $entity = (new UserDomainService())->insert($requestDto->toArray());

        try {
            $this->_i_user_repository->create($entity);
            $this->_i_user_repository->flush();
            CommonUserPush::dispatch(['id'=>$entity->getId()]);
            $this->_i_user_repository->commit();
        } catch (\Exception $e) {
            $this->_i_user_repository->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function edit($requestDto)
    {
        $user = $this->_i_user_repository->find($requestDto->id);
        if (!$user) {
            throw new \Exception("数据不存在");
        }

        $this->_i_user_repository->beginTransaction();

        $entity = (new UserDomainService())->updata($user,$requestDto->toArray());

        try {
            $this->_i_user_repository->create($entity);
            $this->_i_user_repository->flush();
            CommonUserPush::dispatch(['id'=>$entity->getId()]);
            $this->_i_user_repository->commit();
        } catch (\Exception $e) {
            $this->_i_user_repository->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function list($requestDto)
    {
        /*$lists = $this->_i_user_repository->getUserList($requestDto->toArray(), $requestDto->pageSize, $requestDto->page);
        $count = $lists->total();
        foreach ($lists as $key=>$entity) {
            $list[$key] = $entity->toArray();
        }*/


        $data = $this->search($requestDto);
        $lists = $data['data'];
        $count = $data['count'];

        $list = [];
        foreach ($lists as $key=>$value) {
            $list[$key] = $value['_source'];
        }

        return DomainClass::getListResponseDto(compact("count", "list"));
    }

    public function search($requestDto)
    {
        $info[] = ["match"=>["status"=>"ON"]];

        if(isset($requestDto->id) && !empty($requestDto->id)){
            $info[] = ["match"=>["id"=>$requestDto->id]];
        }
        if(isset($requestDto->name) && !empty($requestDto->name)){
            $info[] = ["match_phrase_prefix"=>["name"=>$requestDto->name]];
        }
        if(isset($requestDto->password) && !empty($requestDto->password)){
            $info[] = ["match"=>["password"=>$requestDto->password]];
        }

        $queryInfo['bool']['must'] = $info;
        $searcher['function_score']['query'] = $queryInfo;
        $searcher['function_score']['functions'] = [];
        $searcher['function_score']['score_mode'] = "sum";
        $searcher['function_score']['boost_mode'] = "multiply";

        $order = [];
        if((isset($requestDto->sort) && !empty($requestDto->sort)) && (isset($requestDto->order) && !empty($requestDto->order))){
            $order[] = [$requestDto->sort=>["order"=>$requestDto->order]];
        }

        $esService = new ElasticsearchService('elasticsearch.common_user_index');
        $data = $esService->search(['page'=>$requestDto->page,'pageSize'=>$requestDto->pageSize], $searcher, $order);

        return $data;
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
