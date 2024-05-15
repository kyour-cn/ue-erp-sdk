<?php

namespace kyour\ueerp;

class UeConfig
{
    /**
     * @var string 登录账号
     */
    public $appId = '';

    /**
     * @var string 登录密码(接口的base64值)
     */
    public $appSecret = '';

    public $gateway = 'https://ue.800best.com';

    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $gateway
     */
    public function __construct(string $appId = '', string $appSecret = '', string $gateway = '')
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        if($gateway) {
            $this->gateway = $gateway;
        }
    }

}