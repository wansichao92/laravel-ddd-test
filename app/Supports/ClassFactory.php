<?php

namespace App\Supports;

use App\Services\TestService;
use App\Services\BankInfoService;

class ClassFactory
{
    public static function getTestService()
    {
        return new TestService();
    }

    public static function getBaseExport(array $data, $column, $setStyle)
    {
        return new BaseExport($data, $column, $setStyle);
    }

    public static function getBankInfoService()
    {
        return new BankInfoService();
    }
}
