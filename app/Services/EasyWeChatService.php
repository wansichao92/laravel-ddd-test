<?php

namespace App\Services;

use EasyWeChat\Factory;

class EasyWeChatService
{
    protected $wx;

    public function __construct($key = 'wsc')
    {
        $config = [
            'app_id' => config('weixin.'.$key.'.appId'),
            'secret' => config('weixin.'.$key.'.secret'),
            'http' => [
                'verify' => false,
            ]
        ];
        $this->wx = Factory::miniProgram($config);
    }

    /**
     * 获取所有模板列表
     */
    public function getPrivateTemplates()
    {
        return $this->wx->template_message->getPrivateTemplates();
    }

    /**
     * 获取微信用户信息 并解密
     * @param $iv
     * @param $encryptedData
     * @param $code
     * @param $type user
    overtrue/wechat     * @return  array
     */
    public function getWxUserInfo($iv,$encryptedData,$code,$type = 'user',$session_key = NULL)
    {
        if(!$iv || !$encryptedData || !$code){
            return ['code' => 0 ,'errMsg' => 'empty params'];
        }
        if($session_key === NULL){//如果有session_key 就不需要再去调用微信接口
            //1.先获取session_key
            $session_user = $this->wx->auth->session( $code);
            \Log::channel('user_api')->info($type.'微信解密-session_user',$session_user);
            if(isset($session_user['errcode']) && $session_user['errcode'] != 0){
                return ['code' => 0 ,'errMsg' => $session_user['errmsg']];
            }
            $session_key = $session_user['session_key'];
        }


        //2.再去解密
        $result = $this->wx->encryptor->decryptData($session_key, $iv, $encryptedData);
        \Log::channel('user_api')->info($type.'微信解密结果-session_user',$result);

        if(isset($session_user['errcode']) && $result['code'] != 0){//请求失败
            return ['code' => 0, 'errMsg' => $result['errmsg']];
        }
        $info = [
            'tel' => $result['purePhoneNumber'] ?? '',//手机号
            'unionid' => $result['unionId'] ?? ($session_user['unionid'] ?? ''),
            'openid' => $session_user['openid'] ?? '',
        ];
        if('user' == $type){//用户信息里面已经有openid 和 unionid
            $info['session_key'] = $session_key;
        }
        //请求成功
        return  ['code' => 1,'info' => $info];
    }

    /**
     * 微信模板消息推送
     */
    public function WxPush($open_id, $template_id, $page, $formId, $data)
    {
        return $this->wx->subscribe_message->send(
            [
                'touser' => $open_id,
                'template_id' => $template_id,
                'page' => $page,
                'form_id' => $formId,
                'data' => $data
            ]);
    }

    /**
     * 生产小程序码
     */
    public function createCode($path,$optional = [])
    {
        $response = $this->wx->app_code->get($path, $optional);

        // 保存小程序码到文件
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs(storage_path('app/public/mini_code'), 'code'.time().rand(0000,9999).'.png');
        } else {
            throw new \Exception('生成失败');
        }

        return config('app.url').'/storage/mini_code/'.$filename;
    }
}
