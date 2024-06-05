<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/6/5 下午3:28
 */

namespace Cmslz\DouyinMiniProgram\Application;

use Cmslz\DouyinMiniProgram\Kernel\Exceptions\InvalidArgumentException;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\Response;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithApplication;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * 泛知识
 * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge
 * Class PanKnowledge
 * @package Cmslz\DouyinMiniProgram\Application
 * Created by xiaobai at 2024/6/5 下午3:30
 */
class PanKnowledge
{
    use InteractWithApplication;

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
     */
    public function upload_material($material_path, int $material_type): array
    {
        $appid = $this->application->getAccount()->getAppId();
        $accessToken = $this->application->getAccessToken()->getClientToken(true);
        // 创建表单数据
        $formData = new FormDataPart([
            'appid' => $appid,
            'access_token' => $accessToken,
            'material_type' => strval($material_type),
            'material_file' => DataPart::fromPath($material_path),
        ]);

        // 获取表单头和内容
        $contentType = $formData->getPreparedHeaders()->get('Content-Type')->getBodyAsString();
        $body = $formData->bodyToString();
        $response = $this->application->getClient()->requestCustom('POST', 'auth/entity/upload_material', [
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
     * @throws TransportExceptionInterface
     */
    public function bypartner(array $basic_auth, array $class_auth, string $entity_id): array
    {
        $response = $this->application->getClient()->postJson('auth/entity/bypartner', [
            'access_token' => $this->application->getAccessToken()->getClientToken(true),
            'appid' => $this->application->getAccount()->getAppId(),
            'basic_auth' => $basic_auth,
            'class_auth' => $class_auth,
            'entity_id' => $entity_id
        ]);
        return $this->result($response);
    }

    /**
     * 自营机构/服务商入驻
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge/role/institution-partner-join
     * Created by xiaobai at 2024/6/5 下午6:29
     * @throws InvalidArgumentException
     */
    public function byself(array $basic_auth, array $class_auth): array
    {
        $response = $this->application->getClient()->postJson('auth/entity/byself', [
            'access_token' => $this->application->getAccessToken()->getClientToken(true),
            'appid' => $this->application->getAccount()->getAppId(),
            'basic_auth' => $basic_auth,
            'class_auth' => $class_auth,
        ]);
        return $this->result($response);
    }
}