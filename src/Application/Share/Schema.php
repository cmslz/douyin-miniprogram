<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/9/12 下午4:03
 */

namespace Cmslz\DouyinMiniProgram\Application\Share;

use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithApplication;

class Schema
{
    use InteractWithApplication;

    public function generate(bool $noExpire, array $params)
    {
        $data = [
            'appid' => $this->application->getAccount()->getAppId(),
            'no_expire' => $noExpire
        ];
        if (!$noExpire) {
            $data['expire_time'] = $params['expire_time'];
        }
        if (!empty($params['path'])) {
            $data['path'] = $params['path'];
        }
        if (!empty($params['query'])) {
            $data['query'] = is_array($params['query']) ? $params['query'] : json_encode($params['query'],
                JSON_UNESCAPED_UNICODE);
        }
        $result = $this->application->getOpenClient()->request('POST',
            '/api/apps/v1/url/generate_schema/', [
                'json' => $data
            ]);
        var_dump($result->getContent());
    }
}