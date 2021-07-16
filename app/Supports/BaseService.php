<?php

namespace App\Supports;

use App\Services\TestService;
use App\Services\EasyWeChatService;
use App\Services\BankInfoService;

trait BaseService{

    public static function getTestService()
    {
        return new TestService();
    }

    public static function getEasyWeChatService()
    {
        return new EasyWeChatService();
    }

    public static function getBankInfoService()
    {
        return new BankInfoService();
    }
}
