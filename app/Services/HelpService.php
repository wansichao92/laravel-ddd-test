<?php
namespace App\Services;

use App\Jobs\AppPush;
use App\Jobs\CommonProductPush;
use App\Jobs\ProductPush;
use App\Jobs\CommonUserPush;


class HelpService
{
    protected static $now_time = null;

    public static function getFirstCharter($str){
        if(empty($str)) {return '';}
        $fchar=ord($str[0]);
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str[0]);
        $s1=iconv('UTF-8','gb2312',$str);
        $s2=iconv('gb2312','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc = ord($s[0])*256 + ord($s[1])-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776 || $asc==-9743) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711 || $asc==-9767) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641 || $asc==-7182 || $asc==-6928 ) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631 || $asc==-6745) return 'P';
        if($asc>=-14630&&$asc<=-14150 || $asc==-7703) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return null;
    }

    public static function getTime() {
        if (empty(self::$now_time)) {
            self::$now_time = date("Y-m-d H:i:s", time());
        }
        return self::$now_time;
    }

    /**
     * 支持where数组使用in条件
     *
     * @param string $key 下标
     * @param  array $value 值
     * @return array
     */
    public static function whereIn($key, $value){
        return [function($query) use ($key, $value){
            $query->whereIn($key, $value);
        }];
    }

    /**
     * 支持where数组使用not in条件
     *
     * @param string $key 下标
     * @param  array $value 值
     * @return array
     */
    public static function whereNotIn($key, $value){
        return [function($query) use ($key, $value){
            $query->whereNotIn($key, $value);
        }];
    }

    /**
     * 支持orwhere数组使用in条件
     *
     * @param array $where 条件
     * @return array
     */
    //$query->where('votes', '>', 100)
    //->orWhere('title', '=', 'Admin');
    public static function orWhere($where){
        return [function($query) use ($where){
            foreach ($where as $key => $value) {
                if ($key == 0) {
                    if (is_array($value[0]))
                        $query->where($value);
                    else
                        $query->where([$value]);
                }else{
                    if (is_array($value[0]))
                        $query->orWhere($value);
                    else
                        $query->orWhere([$value]);
                }
            }
        }];
    }

    /**
     * 支持whereNull
     *
     * @param string $key 下标
     * @return array
     */
    public static function whereNull($key){
        return [function($query) use ($key){
            $query->whereNull($key);
        }];
    }

    /**
     * 支持whereNotNull
     *
     * @param string $key 下标
     * @return array
     */
    public static function whereNotNull($key){
        return [function($query) use ($key){
            $query->whereNotNull($key);
        }];
    }

    /**
     * 拼接数组指定字段
     *
     * @param array $array 处理数组
     * @param string $field 指定下标
     * @param string $splice 拼接字符串
     * @return string
     */
    public static function spliceArrayField($array, $field, $splice = '、') {
        $str = '';
        foreach ($array as $item)
        {
            $str = $str.$splice.$item[$field];
        }
        $str = trim($str, $splice);

        return $str;
    }

    /**
     * 获取指定字段数组
     *
     * @param array $array 处理数组
     * @param string $key 指定下标
     * @return array
     */
    public static function getFieldArrayByList($array, $key = 'id') {
        $arr = [];
        foreach ($array as $value) {
            $arr[] = $value[$key];
        }

        return $arr;
    }

    /**
     * 指定key作为数组下标
     *
     * @param array $array 处理数组
     * @param string $key 指定下标
     * @return array
     */
    public static function arrayFieldToKey($array, $field = 'id') {
        $arr = [];
        foreach ($array as $value) {
            $arr[$value[$field]] = $value;
        }

        return $arr;
    }

    /**
     * 指定key分组
     *
     * @param array $array 处理数组
     * @param string $key 指定下标
     * @return array
     */
    public static function arrayToGroup($array, $field = 'id') {
        $group = [];
        foreach ($array as $value) {
            $group[$value[$field]][] = $value;
        }
        return  $group;
    }

    /**
     * 指定key对数组排序
     *
     * @param array $array 处理数组
     * @param string $key 指定下标
     * @return array
     */
    public static function array_sort($arr,$key,$orderby='asc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v){
            $keysvalue[$k] = $v[$key];
        }
        if($orderby== 'asc'){
            asort($keysvalue);
        }else{
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }

    /**
     * 引用算法生成目录结构树
     *
     * @param array $array 处理数组
     * @param string $field_name 下标
     * @param string $parent_field_name 父级下标
     * @param string $children_name 生成的子级下标名
     * @return array
     */
    public static function generateTree($array, $field_name = 'id', $parent_field_name = 'parent_id', $children_name = 'children') {
        //第一步 构造数据
        $items = array();
        foreach($array as $value){
            $items[$value[$field_name]] = $value;
        }
        //第二部 遍历数据 生成树状结构
        $tree = array();
        foreach($items as $key => $item){
            if(isset($items[$item[$parent_field_name]])){
                $items[$item[$parent_field_name]][$children_name][] = &$items[$key];
            }else{
                $tree[] = &$items[$key];
            }
        }
        return $tree;
    }

    /**
     * app推送job
     * @param $data
     */
    public static function appPush($data)
    {
        try{
            return  AppPush::dispatch($data);

        }catch (\Exception $exception){
            \Log::channel('error')->error($exception);
        }

    }

    /**
     * 请求外部接口
     * @param $url
     * @param $data
     * @param string $method
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function client($url, $data ,$method = 'POST')
    {
        $data['verify'] = false;
        $client = (new \GuzzleHttp\Client)->request($method, $url, $data);
        $response = $client->getBody();
        \Log::channel('http_request')->info([
            'url' => request()->path(),
            'user_id' => request()->offsetGet('user_id'),
            'request_url' => $url,
            'request_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'method' => $method,
            'response' => json_encode(@json_decode($response,true), JSON_UNESCAPED_UNICODE)
        ]);
        return $response;
    }


    /**
     * 发送短信
     * @return  true成功 false失败
     */
    public static function sendSms($mobile, $content, $template, $data = '')
    {
//        if (config('sms.open_whitelist')) {
//            if (!in_array($mobile, config('sms.whitelist'))) {
//                return false;
//            }
//        }

        $data = [
            'mobile' => $mobile,
            'content' => $content,
            'template' => $template,
            'data' => $data
        ];
        $response = self::client(config('request_api.openservice_url').'/api/sms/ali/send',['json'=>$data]);
        $response = json_decode($response,true);
        if(isset($response['code']) && $response['code'] === 0){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 计算两个经纬度距离
     * @param $lat1 第一个经度
     * @param $lng1 第一个纬度
     * @param $lat2 第二个经度
     * @param $lng2 第二个纬度
     * @param $km 是否需要转化成千米
     * @return false|float 返回的是距离
     */
    public static function getDistance($lat1, $lng1, $lat2, $lng2, $km = true)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        if($km){
            return round($calculatedDistance/1000,2);
        } else {
            return round($calculatedDistance);
        }
    }

    /**
     * app推送job
     * @param $data
     */
    public static function productPush($data)
    {
        return  ProductPush::dispatch($data);
    }


    public static function openseviceAppPush($data)
    {
        $url = config('request_api.openservice_url').'/api/push/jpush/pushByregistrationId';
        $client = is_string($data['client_id'])?[$data['client_id']]:$data['client_id'];
        $response = HelpService::client($url,[
            'json' => [
                'registration_id' => json_encode($client),
                'message'  => $data['message'],
                'title' => isset($data['title'])?$data['title']:null,
                'target_type' => isset($data['target_type'])?$data['target_type']:0,
                'extras_params' => isset($data['extras_params'])?$data['extras_params']:0
            ]
        ]);
        return json_decode($response, true);
    }

    public static function handleDeviceId($device_id) {
        $device_id = ltrim($device_id, 'ios_');
        $device_id = ltrim($device_id, 'android_');
        return $device_id;
    }

    public static function dataToArray($array) {
        return json_decode(json_encode($array,true),true);
    }

    public static function createPayApi(): \JueSha\PayApi
    {
        $config['domain'] = config('request_api.pay_api_url');
        $config['token'] = config('request_api.pay_api_token');
        return new \JueSha\PayApi($config);
    }

    public static function getExpressCompany($company_name) {
        $companies = config('express.express_company');
        if (empty($companies)) {
            return '';
        }else{
            foreach ($companies as $k => $v){
                if($v == $company_name){
                    return $k;
                }
            }
            return '';
        }
    }

    public static function createUniqid(){
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-';
        $uuid .= substr($chars, 12, 4) . '-';
        $uuid .= substr($chars, 16, 4) . '-';
        $uuid .= substr($chars, 20, 12);
        return $uuid;
    }

    /**
     * 数组列表下划线命名到驼峰命名
     * @param array $list
     * @return array
     */
    public static function arrayListToCamelCase(array $list)
    {
        $newList = [];
        foreach ($list as $key=>$value) {
            foreach ($value as $name => $data) {
                if (is_string($name)) {
                    $newList[$key][self::toCamelCase($name)] = $data;
                } else {
                    $newList[$key][$name] = $data;
                }
            }
        }
        return $newList;
    }

    /**
     * 数组下划线命名到驼峰命名
     * @param array $list
     * @return array
     */
    public static function arrayToCamelCase(array $list)
    {
        $newList = [];
        foreach ($list as $key=>$value) {
            if (is_string($key)) {
                $newList[self::toCamelCase($key)] = $value;
            } else {
                $newList[$key] = $value;
            }
        }
        return $newList;
    }

    /**
     * 下划线命名到驼峰命名
     * @param $str
     * @return mixed|string
     */
    public static function toCamelCase($str)
    {
        $array = explode('_', $str);
        $result = $array[0];
        $len=count($array);
        if($len>1)
        {
            for($i=1;$i<$len;$i++)
            {
                $result.= ucfirst($array[$i]);
            }
        }
        return $result;
    }

    public static function commonProductPush($data)
    {
        return  CommonProductPush::dispatch($data);
    }

    public static function commonUserPush($data)
    {
        return  CommonUserPush::dispatch($data);
    }

    /**
     * 把相差的时间戳转换成日期格式
     * @param $second
     * @return string
     */
    public static function timeToString($second){
        $str = '';
        $day = floor($second/(3600*24));
        $second = $second%(3600*24);//除去整天之后剩余的时间
        $hour = floor($second/3600);
        $second = $second%3600;//除去整小时之后剩余的时间
        $minute = floor($second/60);
        $second = $second%60;//除去整分钟之后剩余的时间
        if($day > 0){
            $str .= $day.'天';
        }
        if($hour > 0){
            $str .= $hour.'时';
        }
        if($minute > 0){
            $str .= $minute.'分';
        }
        if($second > 0){
            $str .= $second.'秒';
        }
        //返回字符串
        return $str;
    }
}
