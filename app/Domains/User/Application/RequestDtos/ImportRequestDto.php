<?php

namespace App\Domains\User\Application\RequestDtos;

use App\Supports\BaseRequestDto;
use Illuminate\Validation\Rule;

class ImportRequestDto extends BaseRequestDto
{
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    public $file;

    public $type;

    public function rules()
    {
        $rule = [
            'type' => ['nullable', Rule::in(['xls','xlsx'])],
        ];

        return array_merge($rule, parent::rules());
    }

    public function message()
    {
        $message = [
            'type.*' => '上传失败，发货物流模版文件格式错误',
        ];

        return array_merge($message, parent::message());
    }
}
