<?php

namespace App\Supports;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;

/**
 * model基类，赋值与校验基础方法
 * Class BaseModel
 */
abstract class BaseModel
{
    protected  $rules = [];

    protected  $messages = [];

    /**
     * 校验参数
     * @param $params
     */
    public function validAndSet($params)
    {
        $rule = $this->rules;
        array_map(function ($key) use ($params, &$rule){
            if(!isset($params[$key])){
                unset($rule[$key]);
            }
        } ,array_keys($rule));
        $validator = \Validator::make($params,$rule,$this->messages);
        if($validator->fails()){
            throw new \Exception($validator->messages()->first());
        }
        foreach ($params as $key=>$value)
        {
            if(method_exists($this,'set'.Str::studly($key))){

                $this->{'set'.Str::studly($key)}($value);
            }
        }
    }

    public function castAs($newClass) {
        $obj = new $newClass;
        foreach (get_object_vars($this) as $key => $name) {
            $obj->$key = $name;
        }
        return $obj;
    }


    public function all(): array
    {
        $data = [];
        $class = new ReflectionClass(ltrim(static::class,'DoctrineProxies\__CG__\\'));

        $properties = $class->getProperties();

        foreach ($properties as $reflectionProperty) {
            if($class->hasMethod('get'.Str::studly($reflectionProperty->getName()))){
                $getter = $class->getMethod('get'.Str::studly($reflectionProperty->getName()));
                $value = $getter->invoke($this);
                if($value && $value instanceof \DateTime){
                    $value = $value->format('Y-m-d H:i:s');
                }
                $data[$reflectionProperty->getName()] = $value ;
            }

        }
        return $data;
    }

    public function toArray($mergeArray = []): array
    {
        $array = $this->all();
        return array_merge($array, $mergeArray);
    }
}
