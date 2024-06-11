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

$info = include_once __DIR__ . '/../info.php';
$env = 'prod';
$config = [
    'appid' => $info[$env]['appid'],
    'secret' => $info[$env]['secret'],
    'env' => $env
];
//$app = \Cmslz\DouyinMiniProgram\Factory::app($config);
//$appid = $app->getAccount()->getAppId();
//$response = $app->getClient()->postJson('api/apps/v1/qrcode/create/', [
//    'appid' => $appid,
//]);
//$result = $response->toArray(false);
//var_dump($result);exit;
$app = \Cmslz\DouyinMiniProgram\Factory::panKnowledge($config);

class Test
{
    use \Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithCache;

    public function upload(
        \Cmslz\DouyinMiniProgram\Application\PanKnowledge\Role $role,
        string $path,
        int $materialType,
        string $cacheKey
    ) {
        $cacheKey .= $materialType;
        $cache = $this->getCache()->get($cacheKey);
        if (empty($cache)) {
            $cache = $role->upload_material($path, $materialType);
            if (empty($cache['path'])) {
                throw new \Cmslz\DouyinMiniProgram\Kernel\Exceptions\BaseException("上传异常");
            }
            $this->getCache()->set($cacheKey, $cache);
            echo "重新获取", PHP_EOL;
        }
        return $cache;
    }
}

$roleApp = $app->role();
$test = new Test();
$expiredTime = '2024-12-01';


## 申请服务商入驻Start
// 申请服务商入驻
//$certificate = $test->upload($roleApp, __DIR__ . '/../营业执照（副）-群友2024.4.2(1).jpg', 1007,
//    '营业执照.jpgv2' . $env);
//$front_path = $test->upload($roleApp, __DIR__ . '/../id_card1.png', 1000, 'id_card1.png' . $env);
//$back_path = $test->upload($roleApp, __DIR__ . '/../id_card2.png', 1000, 'id_card2.png' . $env);
//$cooperation_cases = [
//    $test->upload($roleApp, __DIR__ . '/../教培行业私域运营解决方案v5.2_1650852985507.pdf', 1010,
//        '教培行业私域运营解决方案v5' . $env)['path'],
//];
//$basic_auth = [
//    'entity_type' => 2,
//    'entity_name' => $info['entity_name'],
//    'certificate_type' => 2,
//    'enterprise' => [
//        'certificate_id' => $info['certificate_number'],
//        'certificate_materials' => [
//            [
//                "material_type" => 1007,
//                "material_expiretime" => '长期有效',
//                "material_paths" => [$certificate['path']]
//            ]
//        ],
//        "legal_person" => [
//            "name" => $info['name'],
//            "id_number" => $info['id_number'],
//            "expire_time" => $info['id_expiretime'],
//            'front_path' => $front_path['path'],
//            'back_path' => $back_path['path']
//        ]
//    ]
//];
//$class_auth = [
//    'industry_code' => 10000,
//    'industry_class' => [
//        'first_class' => 0,
//        'second_class' => 0,
//        'third_class' => 0,
//    ],
//    'industry_role' => 3,
//    'partner' => [
//        "company_type" => "企业工商户",
//        "company_addr" => $info['company_addr'],
//        "cooperation_cases" => [
//            [
//                "material_type" => 1010,
//                "material_expiretime" => $expiredTime,
//                "material_paths" => $cooperation_cases
//            ]
//        ]
//    ]
//];
//$result = $roleApp->byself($basic_auth, $class_auth);

// sendbox结果
//array(4) {
//    ["basic_auth_taskid"]=>
//  string(25) "Basic_7376449572094197798"
//    ["class_auth_taskid"]=>
//  string(24) "Role_7376449572094246950"
//    ["entity_id"]=>
//  string(21) "E_7376449572094181414"
//    ["err"]=>
//  array(2) {
//        ["err_code"]=>
//    int(0)
//    ["err_msg"]=>
//    string(0) ""
//  }
//}

// prod结果
//array(5) {
//    ["basic_auth_taskid"]=>
//  string(25) "Basic_7378773674637361206"
//    ["class_auth_taskid"]=>
//  string(24) "Role_7378773674637393974"
//    ["entity_id"]=>
//  string(21) "E_7378773674637344822"
//    ["err"]=>
//  array(2) {
//        ["err_code"]=>
//    int(0)
//    ["err_msg"]=>
//    string(0) ""
//  }
//  ["x-tt-logid"]=>
//  string(34) "202406111014419133262F3322D62C82BC"
//}
# 申请服务商入驻End

# 更新基础认证资质Start
//$certificate = $test->upload($roleApp, __DIR__ . '/../营业执照（副）-群友2024.4.2(1).jpg', 1007,
//    '营业执照.jpgv2' . $env);
//$front_path = $test->upload($roleApp, __DIR__ . '/../id_card1.png', 1000, 'id_card1.png' . $env);
//$back_path = $test->upload($roleApp, __DIR__ . '/../id_card2.png', 1000, 'id_card2.png' . $env);
//$params = [
//    "enterprise" => [
//        "certificate_id" => $info['certificate_number'],
//        "certificate_materials" => [
//            [
//                "material_type" => 1007,
//                "material_expiretime" => '长期有效',
//                "material_paths" => [$certificate['path']]
//            ]
//        ],
//        "enterprise_extra_info" => [
//            'enterprise_certification_type' => "企业工商户",
//            "registered_complete_address" => $info['company_addr']
//        ],
//        "legal_person" => [
//            "name" => $info['name'],
//            "id_number" => $info['id_number'],
//            "expire_time" => $info['id_expiretime'],
//            "front_path" => $front_path['path'],
//            "back_path" => $back_path['path']
//        ]
//    ]
//];
//$result = $roleApp->update_basic_auth('E_7378773674637344822', $info['entity_name'], $params);
//array(3) {
//    ["basic_auth_taskid"]=>
//  string(25) "Basic_7379075105797963776"
//    ["err"]=>
//  array(2) {
//        ["err_code"]=>
//    int(0)
//    ["err_msg"]=>
//    string(0) ""
//  }
//  ["x-tt-logid"]=>
//  string(34) "202406111457259738A938CF03B51F1D4F"
//}
# 更新基础认证资质End


# 更新类目认证资质Start

//$certificate = $test->upload($roleApp, __DIR__ . '/../营业执照（副）-群友2024.4.2(1).jpg', 1007,
//    '营业执照.jpgv2' . $env);
//$logo = $test->upload($roleApp, __DIR__ . '/../群友学院.jpg', 1008, '群友学院.jpg' . $env);
//$front_path = $test->upload($roleApp, __DIR__ . '/../id_card1.png', 1000, 'id_card1.png' . $env);
//$back_path = $test->upload($roleApp, __DIR__ . '/../id_card2.png', 1000, 'id_card2.png' . $env);
//$cooperation_cases = [
//    $test->upload($roleApp, __DIR__ . '/../教培行业私域运营解决方案v5.2_1650852985507.pdf', 1010,
//        '教培行业私域运营解决方案v5' . $env)['path']
//];
//$cooperation_agreement = $test->upload($roleApp, __DIR__ . '/../承诺书.png', 1005, '承诺书.png' . $env);
//$user_front_path = $test->upload($roleApp, __DIR__ . '/../user_id_card1.jpg', 1000, 'user_id_card1.jpg' . $env);
//$user_back_path = $test->upload($roleApp, __DIR__ . '/../user_id_card2.jpg', 1000, 'user_id_card2.jpg' . $env);
//$params = [
//    "partner_entity_id"=> "",
//    "merchant_entity_id"=> "I_7177356117591244844",
//    "industry_code"=> 10000,
//    "class"=> [
//        "first_class"=> 40000,
//        "second_class"=> 40600,
//        "third_class"=> 0
//    ],
//    "industry_role"=> 2,
//    "merchant_qualifications"=> [
//        [
//            "material_type"=> 1003,
//            "material_expiretime"=> "",
//            "material_paths"=> [
//            "certification/resource/74ed50a56eaa6782d96a55f157974720"
//        ]
//        ]
//    ],
//    "institution"=> [
//        "record_name"=> "xxx合作优先公司",
//        "scene_type"=> "线上机构",
//        "subject_type"=> "企业工商户",
//        "logo_uri"=> "certification/resource/74ed50a56eaa6782d96a55f15xxx",
//        "trademark_uri"=> "certification/resource/74ed50a56eaa6782d96a55f1579yyy",
//        "desc"=> "机构描述",
//        "employee"=> [
//            "employee_material"=> [
//                "name"=> "王xx",
//                "id_number"=> "411938171723773",
//                "expire_time"=> "2033-10-10",
//                "front_path"=> "certification/resource/74ed50a56eaa6782d96a55f15xxxx",
//                "back_path"=> "certification/resource/74ed50a56eaa6782d96a55f157xxxxx"
//            ],
//            "cooperation_agreement"=> [
//                "material_type"=> 1005,
//                "material_expiretime"=> "2023-10-10",
//                "material_paths"=> [
//                    "certification/resource/74ed50a56eaa6782d96xxxxf157xxxxx"
//                ]
//            ]
//        ]
//    ]
//];
//$result = $roleApp->update_class_auth('E_7378773674637344822',$params);
# 更新类目认证资质End


# 查询基础认证资质Start
//$result = $roleApp->get_basic_auth('E_7378773674637344822');
# 查询基础认证资质End


# 查询类目认证资质Start
$result = $roleApp->get_class_auth('I_7379135698705793087', 10000, [
    'partner_entity_id' => 'E_7378773674637344822',
    'industry_role' => 1,
    'industry_class' => [
        "first_class" => 40000,
        "second_class" => 40600,
        "third_class" => 0
    ]
]);
# 查询类目认证资质End


# 获取审核任务详情start
// 获取审核任务详情
//$result = $roleApp->get_audit_detail('Role_7378773674637393974',2);
//$result = $roleApp->get_audit_detail('Role_7374629232724672564',2);
// 响应数据
//array(2) {
//    ["err"]=>
//  array(2) {
//        ["err_code"]=>
//    int(0)
//    ["err_msg"]=>
//    string(0) ""
//  }
//  ["qualifications"]=>
//  array(1) {
//        [0]=>
//    array(5) {
//            ["audit_reason"]=>
//      string(0) ""
//            ["audit_status"]=>
//      int(2)
//      ["audit_taskid"]=>
//      string(24) "Role_7376449572094246950"
//            ["expire_time"]=>
//      string(0) ""
//            ["qualifications"]=>
//      array(0) {
//            }
//    }
//  }
//}
# 获取审核任务详情End

# 查看实体IDStart
//$result = $roleApp->query_entity_info($info['user']['id_number']);
//$result = $roleApp->query_entity_info($info['certificate_number']);
//array(2) {
//    ["entity_id"]=>
//  string(21) "E_7376449572094181414"
//    ["err"]=>
//  array(2) {
//        ["err_code"]=>
//    int(0)
//    ["err_msg"]=>
//    string(6) "成功"
//  }
//}

# 查看实体IDEnd


# 新增角色Start
//$certificate = $test->upload($roleApp, __DIR__ . '/../营业执照（副）-群友2024.4.2(1).jpg', 1007,
//    '营业执照.jpgv2' . $env);
//$logo = $test->upload($roleApp, __DIR__ . '/../群友学院.jpg', 1008, '群友学院.jpg' . $env);
//$front_path = $test->upload($roleApp, __DIR__ . '/../id_card1.png', 1000, 'id_card1.png' . $env);
//$back_path = $test->upload($roleApp, __DIR__ . '/../id_card2.png', 1000, 'id_card2.png' . $env);
//$cooperation_cases = [
//    $test->upload($roleApp, __DIR__ . '/../教培行业私域运营解决方案v5.2_1650852985507.pdf', 1010,
//        '教培行业私域运营解决方案v5' . $env)['path']
//];
//$cooperation_agreement = $test->upload($roleApp, __DIR__ . '/../承诺书.png', 1005, '承诺书.png' . $env);
//$user_front_path = $test->upload($roleApp, __DIR__ . '/../user_id_card1.jpg', 1000, 'user_id_card1.jpg' . $env);
//$user_back_path = $test->upload($roleApp, __DIR__ . '/../user_id_card2.jpg', 1000, 'user_id_card2.jpg' . $env);
//$params = [
//    "partner_role_info" => [
//        "partner_info" => [
//            "company_type" => "企业工商户",
//            "company_addr" => $info['company_addr'],
//            "cooperation_cases" => [
//                [
//                    "material_type" => 1010,
//                    "material_expiretime" => $expiredTime,
//                    "material_paths" => $cooperation_cases
//                ]
//            ]
//        ]
//    ],
//    "institution_role_info" => [
//        "institution_info" => [
//            "record_name" => "群友学院",
//            "scene_type" => "线上机构",
//            "subject_type" => "个体工商户",
//            "logo_uri" => $logo['path'],
////            "trademark_uri" => "",
//            "desc" => "支持群友学院的小程序，支持群友学院小程序的运营"
//        ],
//        "institution_class_info" => [
//            "industry_class" => [
//                "first_class" => 0,
//                "second_class" => 0,
//                "third_class" => 0
//            ],
//            "employee" => [
//                "employee_material" => [
//                    "name" => $info['user']['name'],
//                    "id_number" => $info['user']['id_number'],
//                    "expire_time" => $expiredTime,
//                    "front_path" => $user_back_path['path'],
//                    "back_path" => $user_back_path['path']
//                ],
//                "cooperation_agreement" => [
//                    "material_type" => 1005,
//                    "material_expiretime" => $expiredTime,
//                    "material_paths" => [$cooperation_agreement['path']]
//                ]
//            ],
//            "class_material" => [
//                "material_type" => 1005,
//                "material_expiretime" => "",
//                "material_paths" => [$cooperation_agreement['path']]
//            ]
//        ]
//    ]
//];
//$result = $roleApp->add_role('E_7378773674637344822', 3, $params);
# 新增角色End

# 小程序绑定角色Start
//$result = $roleApp->bind_role(10000, 1, 'I_7374629232724607028');
//$result = $roleApp->bind_role('E_7378773674637344822');
//array(1) {
//    ["err"]=>
//  array(2) {
//        ["err_code"]=>
//    int(0)
//    ["err_msg"]=>
//    string(0) ""
//  }
//}
# 小程序绑定角色End

# 查询小程序已绑定的角色Start
//$result = $roleApp->get_bind_list();
//array(2) {
//    ["bind_info_list"]=>
//  array(1) {
//        [0]=>
//    array(5) {
//            ["appid"]=>
//      string(20) "tt5633208ec9056a4201"
//            ["industry_code"]=>
//      int(10000)
//      ["industry_role"]=>
//      int(3)
//      ["merchant_entity_id"]=>
//      string(21) "E_7376449572094181414"
//            ["role_id"]=>
//      string(0) ""
//    }
//  }
//  ["err"]=>
//  array(2) {
//        ["err_code"]=>
//    int(0)
//    ["err_msg"]=>
//    string(0) ""
//  }
//}
# 查询小程序已绑定的角色End


# 代运营服务商帮老师或代运营模式机构入驻Start
// 代运营服务商帮老师或代运营模式机构入驻
//$qualifications_path = $test->upload($roleApp, __DIR__ . '/../承诺书.png', 1001, '承诺书.png');
//$user_front_path = $test->upload($roleApp, __DIR__ . '/../user_id_card1.jpg', 1000, 'user_id_card1.jpg');
//$user_back_path = $test->upload($roleApp, __DIR__ . '/../user_id_card2.jpg', 1000, 'user_id_card2.jpg');
//$basic_auth = [
//    "entity_type" => 1,
//    "entity_name" => $info['user']['name'],
//    "certificate_type" => 1,
//    "individual" => [
//        "name" => $info['user']['name'],
//        "id_number" => $info['user']['id_number'],
//        "expire_time" => $info['user']['id_expiretime'],
//        "front_path" => $user_front_path['path'],
//        "back_path" => $user_back_path['path']
//    ]
//];
//$class_auth = [
//    "industry_code" => 10000,
//    "industry_class" => [
//        "first_class" => 40000,
//        "second_class" => 40600,
//        "third_class" => 0
//    ],
//    "industry_role" => 1,
//    "qualifications" => [
//        [
//            "material_type" => 1001,
//            "material_expiretime" => $expiredTime,
//            "material_paths" => [
//                $qualifications_path['path']
//            ]
//        ]
//    ]
//];
//$result = $roleApp->bypartner($basic_auth, $class_auth, 'E_7378773674637344822');
//array(4) {
//    ["basic_auth_taskid"]=>
//  string(25) "Basic_7374629232724623412"
//    ["class_auth_taskid"]=>
//  string(24) "Role_7374629232724672564"
//    ["entity_id"]=>
//  string(21) "I_7374629232724607028"
//    ["err"]=>
//  array(2) {
//        ["err_code"]=>
//    int(0)
//    ["err_msg"]=>
//    string(0) ""
//  }
//}
# 代运营服务商帮老师或代运营模式机构入驻End

# 新增抖音号绑定\能力授权Start
//$result = $roleApp->enable_mountscope('865093266', 10000, 1, 'I_7379135698705793087', 'aweme_id', [
//    'partner_entity_id' => 'E_7378773674637344822',
//    'mount_scope_list' => [1, 2, 4, 5, 6, 9, 12, 13]
//]);
# 新增抖音号绑定\能力授权End
var_dump($result);
exit;



//$appid = $app->getAccount()->getAppId();
//$response = $app->getClient()->postJson('api/apps/v1/qrcode/create/', [
//    'appid' => $appid,
//]);
//$result = $response->toArray(false);
//var_dump($result);