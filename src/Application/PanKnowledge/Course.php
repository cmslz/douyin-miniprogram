<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/6/6 下午2:01
 */

namespace Cmslz\DouyinMiniProgram\Application\PanKnowledge;

use Cmslz\DouyinMiniProgram\Kernel\Exceptions\BadResponseException;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\BaseException;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\InvalidArgumentException;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\Response;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithApplication;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithMergeAppidAndAccessToken;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Course
{
    use InteractWithApplication;
    use InteractWithMergeAppidAndAccessToken;

    /**
     * @throws BaseException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function result(Response $response, bool $throw = null): array
    {
        $result = $response->toArray($throw);

        if (!empty($result['err_code']) && !empty($throw)) {
            $errorCodes = [
                60000 => '系统错误。尝试重试,持续发生联系工程师。',
                60003 => '必填参数缺失。参考接口文档,检查请求参数的缺失字段。',
                60010 => '实体不存在。检查请求参数实体ID。',
                60011 => '实体基础认证失败。实体基础认证失败，不允许当前操作，等到当前认证结果或者更新基础认证资质。',
                60012 => '实体已存在,无需重复入驻。当前实体已存在，可以直接添加类目。',
                60020 => '类目认证失败。类目认证失败，不允许当前操作，等到当前认证结果或者更新类目认证资质。',
                60021 => '类目已存在。不用重复添加类目。',
                60022 => '类目不存在。类目信息不存在，检查请求类目参数，添加当前类目认证。',
                60023 => '机构信息不匹配。机构主体类型或者机构场景类型和之前信息不一致。',
                60030 => '绑定关系不存在。小程序和当前实体无授权关系。',
                60031 => '机构信息不匹配。重新获取小程序appid 对应的access_token。',
                60032 => '非法appid。修复请求体中的appid 信息。',
                60033 => '非法实体ID。修复请求体中的实体ID 信息。',
                60034 => '小程序和实体不匹配。请求参数有误，小程序的开发者账号和创建实体的开发者账号不一致。',
                60040 => '非法文件路径。材料路径有误，检查资料路径/重新上传资料。',
                60041 => '非法文件大小。文件大小超过限制。',
                60042 => '非法文件类型。资料对应的文件类型不合法。',
                60043 => '文件不存在。文件路径非法，可以重新上传之后再次发起该调用。',
                60044 => '非法文件格式。此类文件不支持当前文件格式。',
                60080 => '非法商户号,该商户号不支持激活。该商户号不是存量商户号，或者为非法商户号。',
                60081 => '商户号不存在。',
                60082 => '商户号尚未激活,请先激活该商户号。',
                60101 => '非法实体类型。请求参数实体类型和当前类型不匹配。',
                60102 => '非法证件类型。对应认证信息和请求中的材料不匹配。',
                60103 => '非法实体名称。实体名称不合规范。',
                60104 => '非法证件ID。',
                60105 => '非法角色类型。',
                60106 => '非法角色类型。',
                60107 => '非法行业类目。',
                60108 => '非法行业资质。',
                60109 => '非法机构信息。',
                60110 => '非法机构备案名称。',
                60111 => '非法机构主体类型或服务商公司类型。',
                60112 => '非法身份证号码。',
                60113 => '非法身份证姓名。',
                60114 => '非法身份证照片路径。',
                60115 => '非法证件过期时间。',
                60116 => '非法证件过期时间。',
                60117 => '非法服务商参数。',
                60118 => '非法服务商信息。',
                60119 => '非法授权书。',
                60120 => '缺失机构和老师的合作证明。',
                60121 => '缺失老师资质信息。',
                60122 => '缺失服务商合作案例。',
                60123 => '缺失营业执照相关资质。',
                60124 => '缺失个人身份证信息。',
                60125 => '缺失小程序授权书。',
                60126 => '非法的任务ID。',
                60127 => '非法审核类型。',
                60128 => '非法机构场景类型。',
                60129 => '小程序无归属。',
                60130 => '资质材料缺失。',
                60131 => '非法操作。',
                60132 => '过期时间格式非法。',
                60133 => '非法机构介绍。',
                60134 => '非法昵称。',
                60135 => '非法老师介绍。',
                60136 => '非法授权关系。',
                60137 => 'C端账户和实体不存在绑定关系。',
                60140 => '小程序没有开启挂载能力。',
                60141 => '能力不可见。',
                10000 => '系统错误',
                10001 => '无效的AccessToken',
                10003 => '实体基础认证失败',
                10004 => 'AppID错误或AppID参数缺失',
                10005 => '无效的商品类型',
                10014 => '商品标题不在字数限制范围内',
                10016 => '无效的商品详情',
                10017 => '无效的购买须知',
                10018 => '无效的商品履约类型',
                10019 => '缺少商品价格信息',
                10020 => '商品价格不在合法范围内',
                10023 => '无效的商品详情文字介绍',
                10026 => '商品详情文字存在敏感词',
                10027 => '商品购买须知存在敏感词',
                10028 => '商品标题存在敏感词',
                10031 => '无效的商品详情uri',
                10032 => '无效的商品履约uri',
                10033 => '无效的商品履约详情',
                10034 => '无效的商品履约详情文字内容或文字内容为空',
                10035 => '无效的资源上传类型',
                10036 => '无效的商品资源上传url或url为空',
                10037 => '商品资源大小超过限制',
                10038 => '商品资源url 404 not found',
                10039 => '商品履约文件uri文件格式不匹配',
                10040 => '无效的商品类目，一级和二级商品类目不匹配或者商品类目不存在',
                10042 => '无效的商品履约列表',
                10043 => '无效的行业类型',
                10044 => '无效的路径列表或路径列表参数缺失',
                10054 => '无效的商品履约名称或商品履约名称为空',
                10055 => '无效的商品资源上传类型或者上传类型不符',
                10056 => '商品参数，商品通用参数或课程参数缺失',
                10057 => '超出每天上传商品次数',
                10059 => '商品已上架',
                10060 => '商品已下架',
                10061 => '无效的商品id列表或商品id列表参数缺失',
                10062 => '商品id数量超出限制',
                10063 => '商品履约内容数量超出限制',
                10065 => '商品详情的富文本里text和uri不能同时存在',
                10066 => '商品详情的富文本text超出字数限制',
                10067 => '无效商品详情的富文本uri，富文本uri不正确或不存在',
                10068 => '商品履约的富文本text和uri不能同时存在',
                10069 => '商品履约的富文本text字数超出限制',
                10082 => '路径已被其他已上架商品使用',
                10083 => '路径已被其他进审商品使用',
                10085 => '缺失锚点信息，锚点信息不可为空',
                10088 => '视频锚点信息不可为空',
                10089 => '视频锚点标题字符数超出限制',
                10092 => '视频锚点标题文案存在敏感词',
                10111 => '缺少教师或者机构id，两者至少需要填写一个，老师和机构id通过资质添加接口获取，审核通过后可使用',
                10112 => '教师或者机构id不存在，请确认已在资质同步接口添加',
                10119 => '课程节数为空或者不在有效范围内',
                10120 => '退款标签为空',
                10121 => '「xx天未学可退」退款标签不可为空',
                10122 => '退款标签中可退天数不在有效范围内',
                10123 => '「学习进度未满xx%可退」退款标签不可为空',
                10124 => '退款标签中可退学习进度百分数不在有效范围内',
                10125 => '退款标签类型不合法',
                10126 => '使用标签内日期格式不正确',
                10128 => '课程结束时间戳不合法，不能小于0，且需要超过当前时间戳',
                10129 => '课程结束时间戳要大于课程开始时间戳',
                10130 => '商品图片uri为空或不正确',
                10132 => '退款标签类型不合法',
                10133 => '课程图片字段为空',
                10135 => '课程节数为空',
                10139 => 'RawPath和CourseId不唯一',
                10140 => 'RawPath和CourseId已被其他已上架商品使用',
                10141 => 'RawPath和CourseId已被其他进审商品使用',
                10143 => '选择多种截止日期计算方式',
                10144 => '无效的动态日期,不在范围内或选择多个选项',
                10145 => '无法在免审接口修改RawPath或CourseId',
                10149 => '审核信息查询类型错误',
                10166 => '固定类型退款标签不可为空',
                10167 => '课程修改信息与库中信息完全一致，无任何修改',
                10168 => '老师/机构资质未授权给小程序',
                10169 => '当前资质不存在该类目',
                10179 => '资源Content-Length小于或者等于0，请检查资源URL后重试',
                10180 => 'course_id或raw_path格式错误',
                10181 => 'course_id或raw_path缺失',
                10182 => '资源URL长度不能超过2048',
                10183 => '课程视频时长过低',
                10184 => '教师和机构id只能存在一个',
                20004 => '资质（教师id/机构id)已被封禁',
                20005 => '资质（教师id/机构id)已失效，或未审核通过',
            ];

            $msg = $errorCodes[$result['err_code']] ?? $result['err_msg'];
            throw new BaseException($msg, $result['err_code']);
        }

        return $result;
    }

    /**
     * 上传课程资源
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/resource-upload
     * @param int $resource_type
     * @param $resource_url
     * @param string $callback_data
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     */
    public function upload_resource(int $resource_type, $resource_url, string $callback_data = ''): array
    {
        $params = [
            'resource_type' => $resource_type,
            'resource_url' => $resource_url,
        ];
        if (!empty($callback_data)) {
            $params['callback_data'] = $callback_data;
        }
        $response = $this->application->getClient()->postJson('product/api/upload_resource',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 查询课程资源上传状态
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/resource-upload-query
     * @param string $resource_uri
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function query_resource_status(string $resource_uri): array
    {
        $params = [
            'resource_uri' => $resource_uri,
        ];
        $response = $this->application->getClient()->postJson('product/api/query_resource_status',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 查询资质
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/qualification-query
     * @param array $object_id_with_classifications
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function query_qualification(array $object_id_with_classifications): array
    {
        $params = [
            'object_id_with_classifications' => $object_id_with_classifications,
        ];
        $response = $this->application->getClient()->postJson('product/api/query_qualification',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 添加课程
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/product-add
     * @param array $product
     * @param int $product_type
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function add(array $product, int $product_type = 1): array
    {
        $params = [
            'product' => $product,
            'product_type' => $product_type,
        ];
        $response = $this->application->getClient()->postJson('product/api/add', $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 修改课程
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/product-modify
     * @param int $product_id
     * @param array $product
     * @param int $product_type
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function modify(int $product_id, array $product, int $product_type = 1): array
    {
        $params = [
            'product_id' => $product_id,
            'product' => $product,
            'product_type' => $product_type,
        ];
        $response = $this->application->getClient()->postJson('product/api/modify', $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 修改课程状态
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/status-modify
     * @param int $product_id
     * @param int $status
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function modify_status(int $product_id, int $status): array
    {
        $params = [
            'product_status_info' => [
                'appid' => $this->application->getAccount()->getAppId(),
                'product_id' => $product_id,
                'status' => $status,
            ]
        ];
        $response = $this->application->getClient()->postJson('product/api/modify_status',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 查询课程
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/product-query
     * @param array $product_ids
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function query(array $product_ids): array
    {
        $params = [
            'product_ids' => $product_ids,
        ];
        $response = $this->application->getClient()->postJson('product/api/query', $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 修改课程免审
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/product-modify-no-audit
     * @param int $product_id
     * @param int $product_type
     * @param array $product
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function modify_no_audit(int $product_id, int $product_type = 1, array $product = []): array
    {
        $params = [
            'product_id' => $product_id,
            'product_type' => $product_type,
        ];
        if (!empty($product)) {
            $params['product'] = $product;
        }
        $response = $this->application->getClient()->postJson('product/api/modify_no_audit',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 查询免审课程
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/query-test-course
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function query_template_info(): array
    {
        $response = $this->application->getClient()->postJson('product/api/query_template_info',
            $this->mergeAppidAndToken());
        return $this->result($response);
    }

    /**
     * 修改商品退款规则
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/product-refund-rule-modify
     * @param array $refund_rule_map
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function modify_refund_rule(array $refund_rule_map): array
    {
        $params = [
            'refund_rule_map' => $refund_rule_map,
        ];
        $response = $this->application->getClient()->postJson('product/api/modify_refund_rule',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 查询可选退款规则
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/refund-meta-query
     * @return array
     * Created by xiaobai at 2024/6/6 下午4:00
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function query_refund_rule_meta(): array
    {
        $response = $this->application->getClient()->postJson('product/api/query_refund_rule_meta',
            $this->mergeAppidAndToken());
        return $this->result($response);
    }

    /**
     * 查询课程类目信息
     * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/product/queryClassInfo
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function query_class_info(): array
    {
        $response = $this->application->getClient()->postJson('product/api/query_class_info',
            $this->mergeAppidAndToken());
        return $this->result($response);
    }
}