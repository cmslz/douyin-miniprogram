<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午11:14
 */

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('memory_limit', '1G');

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require BASE_PATH . '/vendor/autoload.php';
$config = [
    'appid' => 'tt5633208ec9056a4201',
    'secret' => 'c681060cce6f99caa3e8aa018ad6a0e597430c0d',
//    'appid' => 'tt58852e38c7b38c9001',
//    'secret' => '8c811ac41715f17437a42f4b27304d8f7b4cb87a',
    'env' => 'sendbox'
];
//$app = \Cmslz\DouyinMiniProgram\Factory::app($config);
//$appid = $app->getAccount()->getAppId();
//$response = $app->getClient()->postJson('api/apps/v1/qrcode/create/', [
//    'appid' => $appid,
//]);
//$result = $response->toArray(false);
//var_dump($result);exit;

$app = \Cmslz\DouyinMiniProgram\Factory::panKnowledge($config)->role();
// 上传文件素材
$result = $app->upload_material(__DIR__ . '/demo.jpg', 1000);

//$basic_auth = [
//    'certificate_type' => 1,
//    'entity_name' => '何强春',
//    'entity_type' => 1
//];
//$class_auth = [
//    'industry_class' => [
//        "first_class" => 0,
//        "second_class" => 0,
//        "third_class" => 0,
//    ],
//    'industry_code' => 10000,
//    'industry_role' => 3,
//    'partner' => ''
//];
//$app->byself($basic_auth,$class_auth);
var_dump($result);
exit;



//$appid = $app->getAccount()->getAppId();
//$response = $app->getClient()->postJson('api/apps/v1/qrcode/create/', [
//    'appid' => $appid,
//]);
//$result = $response->toArray(false);
//var_dump($result);