<?php

namespace App\Supports;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BaseRequestDto extends FlexibleDataTransferObject
{
        public function __construct(array $parameters = [])
        {
            try{
                parent::__construct($parameters);
            }catch (\TypeError $error){
                throw new \Exception($error->getMessage());
            }

            if(!empty($this->rules())){
                $validator = \Validator::make($parameters,$this->rules(),$this->message());
                if($validator->fails()){
                    throw new \Exception($validator->messages()->first());
                }
            }

        }

        public function rules()
        {
            return [];
        }

        public function message()
        {
            return [];
        }

        public function toArray($mergeArray = []): array
        {
            $array = parent::toArray();
            return array_merge($array, $mergeArray);
        }
}
