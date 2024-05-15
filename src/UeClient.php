<?php declare (strict_types = 1);

namespace kyour\ueerp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use kyour\ueerp\exception\UeRequestException;

class UeClient
{
    /**
     * @var UeConfig 配置信息
     */
    private $config;

    /**
     * @var string 登录后的鉴权token
     */
    private $token;

    public function __construct(UeConfig $config)
    {
        $this->config = $config;

    }

    /**
     * 尝试登录获取token
     * @return string
     * @throws UeRequestException
     */
    public function getToken(): string
    {
        // 登录参数
        $data = [
            'username' => $this->config->appId,
            'password' => $this->config->appSecret,
            'rememberMe' => true,
            'loginEnv' => 'u3',
            'platformLogin' => false,
        ];

        $url = $this->config->gateway. "/ue/erp/cloud/domain/security/login";

        $client = new Client();

        try{
            $response = $client->request('POST', $url, [
                'json' => $data,
            ]);
        }catch (GuzzleException $ge) {
            throw new UeRequestException($ge->getMessage());
        }

        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();
            $body = json_decode($body->getContents(), true);
            if($body['success']) {
                $this->token = $body['data']['oauthToken']['access_token'];
            }else{
                throw new UeRequestException($body['error']['errorMessage']);
            }
        }else{
            throw new UeRequestException('请求失败');
        }

        return $this->token;
    }


}