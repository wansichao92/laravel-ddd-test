<?php

namespace App\Domains\User\Supports;

use App\Domains\User\Application\Services\UserAppService;
use App\Domains\User\Application\RequestDtos\AddRequestDto;
use App\Domains\User\Application\RequestDtos\ListRequestDto;
use App\Domains\User\Application\RequestDtos\ImportRequestDto;
use App\Domains\User\Application\ResponseDtos\ListResponseDto;
use App\Domains\User\Supports\Enums\UserStatusEnum;
use App\Domains\User\Domain\Models\User\CommonUser;

class DomainClass
{
    public static function getUserAppService($repository)
    {
        return new UserAppService($repository);
    }

    public static function getAddRequestDto($data)
    {
        return new AddRequestDto($data);
    }

    public static function getListRequestDto($data)
    {
        return new ListRequestDto($data);
    }

    public static function getImportRequestDto($data)
    {
        return new ImportRequestDto($data);
    }

    public static function getListResponseDto($data)
    {
        return new ListResponseDto($data);
    }

    public static function getUserStatusEnum()
    {
        return new UserStatusEnum();
    }

    public static function getCommonUser()
    {
        return new CommonUser();
    }

    public static function setException($message)
    {
        throw new \Exception($message);
    }
}
