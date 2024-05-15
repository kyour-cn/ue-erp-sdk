<?php

use kyour\ueerp\exception\UeRequestException;
use kyour\ueerp\UeClient;
use kyour\ueerp\UeConfig;

require_once __DIR__ . '/../vendor/autoload.php';

// 正式使用时，token应单独维护，不能每次请求都重新登录
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

echo "登录成功，开始请求数据\n";

// 获取商品列表
$url = '/core/ajax/organizationSpu/getOrgSpuList';

$data = [
    'brandId' => null,
    'categoryId' => null,
    'keyWord' => '',
    'syncStatus' => '',
    'classificationId' => null,
    'pageNum' => 1,
    'pageSize' => 10
];
try {
    $res = $client->request('POST', $url, $data);
}catch (UeRequestException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

var_dump($res);