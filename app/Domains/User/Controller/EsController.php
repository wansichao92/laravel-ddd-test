<?php

namespace App\Domains\User\Controller;

use App\Http\Controllers\Controller;
use App\Domains\User\Supports\DomainClass;
use App\Domains\User\Base\Repository\UserRepository;

class EsController extends Controller
{
    public function add(UserRepository $repository)
    {
        $data = request()->input('data');
        $requestDto = DomainClass::getAddRequestDto($data);
        $appService = DomainClass::getUserAppService($repository);
        return $this->success($appService->add($requestDto));
    }
}
