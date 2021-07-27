<?php

namespace App\Domains\User\Application\RequestDtos;

use App\Supports\BaseRequestDto;
use Illuminate\Validation\Rule;

class AddRequestDto extends BaseRequestDto
{
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    /** @var string $name */
    public $name;

    /** @var string $password */
    public $password;

    /** @var string|null $status */
    public $status = 'ON';

    public function rules()
    {
        return [
            'name' => 'required',
            'password' => 'required',
        ];
    }

    public function message()
    {
        return [
            'name.required' => '姓名不能为空',
            'password.required' => '密码不能为空',
        ];
    }
}
