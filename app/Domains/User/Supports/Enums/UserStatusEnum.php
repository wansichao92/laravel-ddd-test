<?php

namespace App\Domains\User\Supports\Enums;

use App\Supports\BaseEnum;

class UserStatusEnum extends BaseEnum
{
    const ON = 'ON';
    const OFF = 'OFF';

    public static $texts = [
        self::ON => '正常',
        self::OFF => '关闭',
    ];
}
