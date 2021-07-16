<?php

namespace App\Domains\User\Application\ResponseDtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ListResponseDto extends FlexibleDataTransferObject
{
    /** @var App\Domains\User\Application\ResponseDtos\ListDataResponseDto[] */
    public $list = [];

    /** @var int $count */
    public $count = 0;

    public function all() : array
    {
        foreach ($this->list as $val) {
            $val->all();
        }
        return parent::all();
    }
}
