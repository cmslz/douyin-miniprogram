<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/9/12 下午2:40
 */

namespace Cmslz\DouyinMiniProgram\Application\UserClient;

use Cmslz\DouyinMiniProgram\Kernel\Exceptions\HttpException;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithApplication;

class Utils
{
    use InteractWithApplication;

    public function codeToSession(string $code, string $anonymous_code = ''): array
    {
        $response = $this->application->getTouTiAoClient()->request('POST', '/api/apps/v2/jscode2session', [
            'json' => [
                'appid' => $this->application->getAccount()->getAppId(),
                'secret' => $this->application->getAccount()->getSecret(),
                'anonymous_code' => $anonymous_code,
                'code' => $code
            ],
        ], false)->toArray(false);
        if(!empty($response['err_no'])){
            throw new HttpException('code2Session error: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        if (empty($response['data']['openid'])) {
            throw new HttpException('code2Session error: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return $response['data'];
    }

    public function decryptSession(string $session_key): string
    {
        // 假设 session_key 和 post_body
        $post_body = array("foo" => "bar");

        // 将 post_body 转换为 JSON 字符串
        $json_body = json_encode($post_body);

        // 使用 session_key 和 json_body 计算签名
        return hash_hmac('sha256', $json_body, $session_key);
    }
}