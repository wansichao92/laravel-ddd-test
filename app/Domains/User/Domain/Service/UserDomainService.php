<?php

namespace App\Domains\User\Domain\Service;

use App\Domains\User\Domain\Models\User\CommonUser;
use App\Domains\User\Supports\Enums\UserStatusEnum;
use Illuminate\Support\Collection;

class UserDomainService
{
    public function insert(array $requestDto)
    {
        $commonUser = new CommonUser();
        $commonUser->validAndSet($requestDto);
        $commonUser->setStatus(UserStatusEnum::ON);

        return $commonUser;
    }

    public function updata(CommonUser $commonUser, array $requestDto)
    {
        $commonUser->validAndSet($requestDto);
        return $commonUser;
    }

    public function importFileToData($importRequestDto)
    {
        $match = preg_match('/^(data:\s*application\/(.*);base64,)/', $importRequestDto->file, $result);

        if(!$match){
            throw new \Exception('文件格式错误');
        }

        $base64_image = str_replace($result[1], '', $importRequestDto->file);
        $file_content = base64_decode($base64_image);
        $file_type = $importRequestDto->type;

        $file_name = time().".{$file_type}";

        \Storage::put($file_name,$file_content);

        $rows = \Excel::toArray(new Collection(),storage_path('app'.DIRECTORY_SEPARATOR.$file_name));

        return $rows;
    }
}
