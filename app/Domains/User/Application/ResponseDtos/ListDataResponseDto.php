<?php

namespace App\Domains\User\Application\ResponseDtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;
use App\Domains\User\Supports\Enums\UserStatusEnum;

class ListDataResponseDto extends FlexibleDataTransferObject
{
    /** @var string $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var string $password */
    public $password;

    /** @var string|null $status */
    public $status;

    /** @var string|null $createdAt */
    public $createdAt;


    public function all() : array
    {
        if (!empty($this->createdAt)) {
            $this->createdAt = date('Y-m-d H:i:s', strtotime($this->createdAt));
        }
        $this->status = UserStatusEnum::getText($this->status);

        return parent::all();
    }
}
