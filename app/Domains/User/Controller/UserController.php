<?php

namespace App\Domains\User\Controller;

use App\Http\Controllers\Controller;
use App\Domains\User\Supports\DomainClass;
use App\Domains\User\Base\Repository\UserRepository;

class UserController extends Controller
{
    public function add(UserRepository $repository)
    {
        $data = ['name'=>'test08', 'password'=>'$test08$Password'];
        $requestDto = DomainClass::getAddRequestDto($data);
        $appService = DomainClass::getUserAppService($repository);
        return $this->success($appService->add($requestDto));
    }

    public function list(UserRepository $repository)
    {
        $data = ['id'=>'', 'name'=>'', 'pageSize'=>10, 'page'=>1];
        $requestDto = DomainClass::getListRequestDto($data);
        $appService = DomainClass::getUserAppService($repository);
        return $this->success($appService->list($requestDto));
    }

    public function export(UserRepository $repository)
    {
        $data = ['id'=>'', 'name'=>'', 'status'=>'OFF', 'pageSize'=>999999, 'page'=>1];
        $requestDto = DomainClass::getListRequestDto($data);
        $appService = DomainClass::getUserAppService($repository);
        return $this->success($appService->export($requestDto));
    }

    public function import(UserRepository $repository)
    {
        $base64_data = base64_encode(file_get_contents(config('app.url') .'storage/app/1620723594.xls.xls'));
        $base64_file = 'data:application;base64,'.$base64_data;
        $data = ['type'=>'xls', 'file'=>$base64_file];
        $requestDto = DomainClass::getImportRequestDto($data);
        $appService = DomainClass::getUserAppService($repository);
        return $this->success($appService->import($requestDto));
    }
}
