<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/6/6 上午11:09
 */

namespace Cmslz\DouyinMiniProgram\Application\PanKnowledge;

use Cmslz\DouyinMiniProgram\Kernel\Exceptions\BadResponseException;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\BaseException;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\InvalidArgumentException;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\Response;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithApplication;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithMergeAppidAndAccessToken;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Role
{
    use InteractWithApplication;
    use InteractWithMergeAppidAndAccessToken;

    /**
     * 上传材料
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/upload-qualification-materials
     * @param $material_path
     * @param int $material_type
     * IdentityCard=1000身份证文件限制大小：<= 20MB文件格式要求：jpeg、jpg、png
     * TeacherQualification=1001老师教师资格证文件限制大小：<= 20MB文件格式要求：jpeg、jpg、png
     * TeacherSpecializedDegree=1002教师专业性证书-专业性学历文件限制大小：<= 20MB文件格式要求：jpeg、jpg、png
     * TeacherProfessionalCertificate=1003教师专业性证书-专业认证文件限制大小：<= 20MB文件格式要求：jpeg、jpg、png
     * TeacherSpecialCertificate=1004教师特殊课程类目认证文件限制大小：<= 20MB文件格式要求：jpeg、jpg、png
     * TeacherAndInstitutionProtocol=1005机构和老师之间的合作证明文件限制大小：<= 20MB文件格式要求：jpeg、jpg、png【开放平台】泛知识老师vs机构合作声明
     * AuthLetter=1006授权函(在泛知识行业是服务商和老师的授权书)文件限制大小：<= 20MB文件格式要求：jpeg、jpg、png【开放平台】泛知识机构/老师《原创声明/授权声明》模板
     * InstitutionBizLicense=1007机构营业执照照片文件限制大小：<= 5MB文件格式要求：jpeg、jpg、png
     * InstitutionLogoIcon=1008机构logo照片文件限制大小：<= 5MB文件格式要求：jpeg、jpg、png
     * InstitutionTradeMark=1009机构注册证图片文件限制大小：<= 5MB文件格式要求：jpeg、jpg、png
     * CooperationCase=1010服务商合作案例文件限制大小：<= 5MB文件格式要求：pdf请提供过往与客户合作的成功案例或合作协议，需包含客户名称、解决方案、成果等信息
     * InstitutionSpecialCertificate=1011机构特殊课程类目认证文件限制大小：<= 5MB文件格式要求：jpeg、jpg、png
     * AppIDIcon=1012老师在小程序上的头像文件限制大小：<= 5MB文件格式要求：jpeg、jpg、png
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function upload_material($material_path, int $material_type): array
    {
        // 创建表单数据
        $formData = new FormDataPart($this->mergeAppidAndToken([
            'material_type' => strval($material_type),
            'material_file' => DataPart::fromPath($material_path)
        ]));
        // 获取表单头和内容
        $contentType = $formData->getPreparedHeaders()->get('Content-Type')->getBodyAsString();
        $body = $formData->bodyToString();
        $response = $this->application->getTouTiAoClient()->requestCustom('POST', 'auth/entity/upload_material', [
            'headers' => [
                'Content-Type' => $contentType
            ],
            'body' => $body
        ]);
        return $this->result($response);
    }

    /**
     * 代运营服务商帮老师或代运营模式机构入驻
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/partner-help-teacher-institution-join
     * Created by xiaobai at 2024/6/5 下午6:24
     * @param array $basic_auth
     * @param array $class_auth
     * @param string $entity_id
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
    public function bypartner(array $basic_auth, array $class_auth, string $entity_id): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/bypartner', $this->mergeAppidAndToken([
            'basic_auth' => $basic_auth,
            'class_auth' => $class_auth,
            'entity_id' => $entity_id
        ]));
        return $this->result($response);
    }

    /**
     * 自营机构/服务商入驻
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/institution-partner-join
     * Created by xiaobai at 2024/6/5 下午6:29
     * @param array $basic_auth
     * @param array $class_auth
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
    public function byself(array $basic_auth, array $class_auth): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/byself', $this->mergeAppidAndToken([
            'basic_auth' => $basic_auth,
            'class_auth' => $class_auth,
        ]));
        return $this->result($response);
    }

    /**
     * 新增角色
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/add-role
     * Created by xiaobai at 2024/6/6 上午10:02
     * @param string $entity_id
     * @param int $industry_role
     * @param array $params
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
    public function add_role(string $entity_id, int $industry_role, array $params): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/add_role',
            $this->mergeAppidAndToken(array_merge($params, [
                'entity_id' => $entity_id,
                'industry_role' => $industry_role
            ])));
        return $this->result($response);
    }

    /**
     * 查询基础认证资质
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/query-basic-qualification
     * @param string $entity_id
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException Created by xiaobai at 2024/6/6 上午10:11
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function get_basic_auth(string $entity_id): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/get_basic_auth',
            $this->mergeAppidAndToken([
                'entity_id' => $entity_id
            ]));
        return $this->result($response);
    }

    /**
     * 更新基础认证资质
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/update-basic-qualification
     * @param string $merchant_entity_id
     * @param string $entity_name
     * @param array $params
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException Created by xiaobai at 2024/6/6 上午10:14
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function update_basic_auth(string $merchant_entity_id, string $entity_name, array $params): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/update_basic_auth',
            $this->mergeAppidAndToken(array_merge($params, [
                'entity_name' => $entity_name,
                'merchant_entity_id' => $merchant_entity_id
            ])));
        return $this->result($response);
    }

    /**
     * 查询类目认证资质
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/query-category-qualification
     * @param string $merchant_entity_id
     * @param int $industry_code
     * @param array $params
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException Created by xiaobai at 2024/6/6 上午10:16
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function get_class_auth(string $merchant_entity_id, int $industry_code = 10000, array $params = []): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/get_class_auth',
            $this->mergeAppidAndToken(array_merge($params, [
                'merchant_entity_id' => $merchant_entity_id,
                'industry_code' => $industry_code
            ])));
        return $this->result($response);
    }

    /**
     * 更新类目认证资质
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/update-category-qualification
     * @param string $merchant_entity_id
     * @param array $params
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException Created by xiaobai at 2024/6/6 上午10:17
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function update_class_auth(string $merchant_entity_id, array $params = []): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/update_class_auth',
            $this->mergeAppidAndToken(array_merge($params, [
                'merchant_entity_id' => $merchant_entity_id
            ])));
        return $this->result($response);
    }

    /**
     * 添加类目认证资质
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/add-category-qualification
     * Created by xiaobai at 2024/6/6 上午10:18
     * @param string $merchant_entity_id
     * @param array $params
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
    public function add_class_auth(string $merchant_entity_id, array $params = []): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/add_class_auth',
            $this->mergeAppidAndToken(array_merge($params, [
                'merchant_entity_id' => $merchant_entity_id
            ])));
        return $this->result($response);
    }

    /**
     * 获取审核任务详情
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/get-audit-task-details
     * @param string $auth_taskid
     * @param int $auth_type
     * @return array
     * @throws BadResponseException
     * @throws BaseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException Created by xiaobai at 2024/6/6 上午10:20
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function get_audit_detail(string $auth_taskid, int $auth_type): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/get_audit_detail',
            $this->mergeAppidAndToken([
                'auth_taskid' => $auth_taskid,
                'auth_type' => $auth_type
            ]));
        return $this->result($response);
    }

    /**
     * 小程序绑定角色
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/miniapp-binding-role
     * @param int $industry_code
     * @param int $merchant_industry_role
     * @param string $merchant_entity_id
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
    public function bind_role(
        string $merchant_entity_id,
        int $merchant_industry_role = 3,
        int $industry_code = 10000
    ): array {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/bind_role',
            $this->mergeAppidAndToken([
                'industry_code' => $industry_code,
                'merchant_industry_role' => $merchant_industry_role,
                'merchant_entity_id' => $merchant_entity_id
            ]));
        return $this->result($response);
    }

    /**
     * 小程序解除绑定角色
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/miniapp-unbinding-role
     * @param int $industry_code
     * @param int $merchant_industry_role
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
    public function unbind_role(int $industry_code, int $merchant_industry_role): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/unbind_role',
            $this->mergeAppidAndToken([
                'industry_code' => $industry_code,
                'merchant_industry_role' => $merchant_industry_role
            ]));
        return $this->result($response);
    }

    /**
     * 查询小程序已绑定的角色
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/query-miniapp-bound-role
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
    public function get_bind_list(): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/get_bind_list',
            $this->mergeAppidAndToken());
        return $this->result($response);
    }

    /**
     * 角色授权小程序
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/role-authorization-miniapp
     * @param string $desc
     * @param string $icon
     * @param int $industry_code
     * @param int $merchant_industry_role
     * @param string $nick
     * @param array $params
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
    public function auth_role(
        string $desc,
        string $icon,
        int $industry_code,
        int $merchant_industry_role,
        string $nick,
        array $params = []
    ): array {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/auth_role',
            $this->mergeAppidAndToken(array_merge($params, [
                'desc' => $desc,
                'icon' => $icon,
                'industry_code' => $industry_code,
                'merchant_industry_role' => $merchant_industry_role,
                'nick' => $nick
            ])));
        return $this->result($response);
    }

    /**
     * 解除授权小程序
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/deauthorization-miniapp
     * @param int $merchant_entity_id
     * @param int $merchant_industry_role
     * @param string $partner_entity_id
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
    public function unauth_role(
        int $merchant_entity_id,
        int $merchant_industry_role,
        string $partner_entity_id = ''
    ): array {
        $params = [
            'merchant_entity_id' => $merchant_entity_id,
            'merchant_industry_role' => $merchant_industry_role,
        ];
        if (!empty($partner_entity_id)) {
            $params['partner_entity_id'] = $partner_entity_id;
        }
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/unauth_role',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 查询授权小程序
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/query-authorization-applet
     * @param int $merchant_entity_id
     * @param int $merchant_industry_role
     * @param string $partner_entity_id
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
    public function get_appid_auth(
        int $merchant_entity_id,
        int $merchant_industry_role,
        string $partner_entity_id = ''
    ): array {
        $params = [
            'merchant_entity_id' => $merchant_entity_id,
            'merchant_industry_role' => $merchant_industry_role,
        ];
        if (!empty($partner_entity_id)) {
            $params['partner_entity_id'] = $partner_entity_id;
        }
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/get_appid_auth',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 更新授权小程序授权信息
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/update-authorization-letter-of-miniapp
     * @param string $desc
     * @param string $icon
     * @param int $industry_code
     * @param int $merchant_industry_role
     * @param string $nick
     * @param array $params
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
    public function update_authletter(
        string $desc,
        string $icon,
        int $industry_code,
        int $merchant_industry_role,
        string $nick,
        array $params = []
    ): array {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/update_authletter',
            $this->mergeAppidAndToken(array_merge($params, [
                'desc' => $desc,
                'icon' => $icon,
                'industry_code' => $industry_code,
                'merchant_industry_role' => $merchant_industry_role,
                'nick' => $nick
            ])));
        return $this->result($response);
    }

    /**
     * 查询抖音号绑定、能力授权
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/query-mountscope
     * @param int $industry_code
     * @param int $industry_role
     * @param string $merchant_entity_id
     * @param string $id
     * @param string $column ID字段  openid|aweme_id
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
    public function query_mountscope(
        int $industry_code,
        int $industry_role,
        string $merchant_entity_id,
        string $id,
        string $column = 'openid'
    ): array {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/query_mountscope',
            $this->mergeAppidAndToken([
                'industry_code' => $industry_code,
                'industry_role' => $industry_role,
                'merchant_entity_id' => $merchant_entity_id,
                $column => $id,
            ]));
        return $this->result($response);
    }

    /**
     * 新增抖音号绑定、能力授权
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/enable-mountscope
     * @param string $id
     * @param $industry_code
     * @param int $industry_role
     * @param string $merchant_entity_id
     * @param string $column
     * @param array $params
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
    public function enable_mountscope(
        string $id,
        $industry_code,
        int $industry_role,
        string $merchant_entity_id,
        string $column = 'aweme_id',
        array $params = []
    ): array {
        $params['industry_code'] = $industry_code;
        $params['industry_role'] = $industry_role;
        $params['merchant_entity_id'] = $merchant_entity_id;
        if ($column == 'c_user') {
            $params[$column] = [
                'client_key' => $this->application->getAccount()->getAppId(),
                'openid' => $id
            ];
        } else {
            $params[$column] = $id;
        }
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/enable_mountscope',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 解除抖音号绑定、解除能力授权
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/unbind-mountscope
     * @param string $id
     * @param $industry_code
     * @param int $industry_role
     * @param string $merchant_entity_id
     * @param string $partner_entity_id
     * @param string $column
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
    public function unbind_account(
        string $id,
        $industry_code,
        int $industry_role,
        string $merchant_entity_id,
        string $partner_entity_id = '',
        string $column = 'aweme_id',
    ): array {
        $params = [
            'industry_code' => $industry_code,
            'industry_role' => $industry_role,
            'merchant_entity_id' => $merchant_entity_id,
        ];
        if ($column == 'c_user') {
            $params[$column] = [
                'client_key' => $this->application->getAccount()->getAppId(),
                'openid' => $id
            ];
        } else {
            $params[$column] = $id;
        }
        if (!empty($partner_entity_id)) {
            $params['partner_entity_id'] = $partner_entity_id;
        }
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/unbind_account',
            $this->mergeAppidAndToken($params));
        return $this->result($response);
    }

    /**
     * 查询实体ID
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/query-entity-id
     * @param string $certificate_id
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
    public function query_entity_info(string $certificate_id): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/query_entity_info',
            $this->mergeAppidAndToken([
                'certificate_id' => $certificate_id,
            ], true, ['appid' => 'app_id']));
        return $this->result($response);
    }

    /**
     * 查询实体已绑定抖音号列表
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/query-mountlist
     * @param int $industry_code
     * @param int $industry_role
     * @param string $merchant_entity_id
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
    public function query_bind_toc_list(int $industry_code, int $industry_role, string $merchant_entity_id): array
    {
        $response = $this->application->getTouTiAoClient()->postJson('auth/entity/query_bind_toc_list',
            $this->mergeAppidAndToken([
                'industry_code' => $industry_code,
                'industry_role' => $industry_role,
                'merchant_entity_id' => $merchant_entity_id,
            ]));
        return $this->result($response);
    }

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
            ];
            $msg = $errorCodes[$result['err_code']] ?? $result['err_msg'];
            throw new BaseException($msg, $result['err_code']);
        }
        return $result;
    }
}