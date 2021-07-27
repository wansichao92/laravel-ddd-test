<?php

namespace App\Domains\User\Application\RequestDtos;

use App\Domains\User\Supports\DomainClass;
use App\Supports\BaseRequestDto;
use Illuminate\Validation\Rule;

class ListRequestDto extends BaseRequestDto
{
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    /** @var string|int|null $id */
    public $id;

    /** @var string|null $name */
    public $name;

    /** @var string|null $password */
    public $password;

    /** @var string|null $status */
    public $status;

    /** @var int|null $pageSize */
    public $pageSize;

    /** @var int|null $page */
    public $page;

    public function rules()
    {
        return [
            'id' => 'nullable|min:1',
            'name' => 'nullable',
            'status' => ['nullable', Rule::in(DomainClass::getUserStatusEnum()::getConstList())],
            'page' => 'present|nullable|integer|min:1',
            'pageSize' => 'present|nullable|integer'
        ];
    }

    public function message()
    {
        return [
            'id.*' => 'id参数类型错误',
            'name.*' => 'name参数类型错误',
            'status.*' => 'status参数类型错误',
            'page.*' => '页码参数错误',
            'pageSize.*' => '每页条数参数错误'
        ];
    }
}
