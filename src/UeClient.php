<?php declare (strict_types=1);

namespace kyour\ueerp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use kyour\ueerp\exception\UeAuthException;
use kyour\ueerp\exception\UeRequestException;
use Throwable;

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
     * @return array
     * @throws UeRequestException
     */
    public function login(): array
    {
        // 登录参数
        $data = [
            'username' => $this->config->appId,
            'password' => $this->config->appSecret,
            'rememberMe' => true,
            'loginEnv' => 'u3',
            'platformLogin' => false,
        ];

        $url = $this->config->gateway . "/ue/erp/cloud/domain/security/login";

        $client = new Client();

        try {
            $response = $client->request('POST', $url, [
                'json' => $data,
            ]);
        } catch (GuzzleException $ge) {
            throw new UeRequestException($ge->getMessage());
        }

        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();
            $body = json_decode($body->getContents(), true);
            if ($body['success']) {
                $this->token = $body['data']['oauthToken']['access_token'];
                return $body['data'];
            } else {
                throw new UeRequestException($body['error']['errorMessage']);
            }
        } else {
            throw new UeRequestException('请求失败');
        }
    }

    /**
     * 设置token
     * @param $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * 获取token
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $params
     * @return mixed
     * @throws UeRequestException|UeAuthException
     */
    public function request(string $method, string $url, array $data = [], array $params = [])
    {
        $client = new Client();

        // 判断是否有token
        if (empty($this->token)) {
            throw new UeAuthException('没有token', -1);
        }

        try {

            // 添加token
            if (isset($params['headers'])) {
                $params['headers'] = array_merge($params['headers'], [
                    'Cookie' => "thanos_op_token=$this->token;",
                ]);
            } else {
                $params['headers'] = [
                    'Cookie' => "thanos_op_token=$this->token;",
                ];
            }
            // 添加数据
            $params['json'] = $data;

            // 发送请求
            $response = $client->request($method, $this->config->gateway . $url, $params);
        } catch (GuzzleException $ge) {
            throw new UeRequestException($ge->getMessage());
        }

        if ($response->getStatusCode() == 200) {
            $body = $response->getBody()->getContents();
            $body = json_decode($body, true);

            if (isset($body['code']) and $body['code'] == '401') {
                throw new UeAuthException('token失效', -1);
            }
            return $body;
        } else {
            throw new UeRequestException('请求失败');
        }
    }

    /**
     * 检测token是否有效
     * @return bool
     */
    public function checkToken(): bool
    {
        $url = "/core/ajax/organization/ownedList";

        try {
            $this->request('GET', $url);
        } catch (Throwable $e) {
            return false;
        }
        return true;
    }

}