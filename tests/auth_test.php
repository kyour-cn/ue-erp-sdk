<?php

use kyour\ueerp\exception\UeRequestException;
use kyour\ueerp\UeClient;
use kyour\ueerp\UeConfig;

require_once __DIR__ . '/../vendor/autoload.php';

$appid = '';
$appSecret = '';

// 配置信息
$config = new UeConfig($appid, $appSecret);

// 创建客户端
$client = new UeClient($config);

// 登录
try{
    $res = $client->getToken();
}catch (UeRequestException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

var_dump($res);