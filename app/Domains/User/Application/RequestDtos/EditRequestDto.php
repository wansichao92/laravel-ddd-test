<?php

namespace App\Domains\User\Application\RequestDtos;

use App\Supports\BaseRequestDto;
use Illuminate\Validation\Rule;

class EditRequestDto extends BaseRequestDto
{
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    /** @var int|string $id */
    public $id;

    /** @var string|null $name */
    public $name;

    /** @var string|null $password */
    public $password;

    /** @var string|null $status */
    public $status;

    public function rules()
    {
        return [
            'name' => 'nullable|string',
            'password' => 'nullable|string',
            'status' => 'nullable|string',
        ];
    }

    public function message()
    {
        return [
            'name.*' => '姓名字段类型错误',
            'password.*' => '密码字段类型错误',
            'status.*' => '状态字段类型错误',
        ];
    }
}
