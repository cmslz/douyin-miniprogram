<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/6/6 上午11:11
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

trait InteractWithMergeAppidAndAccessToken
{
    private function mergeAppidAndToken(array $data = [], bool $useOldToken = true): array
    {
        return array_merge($data, [
            'access_token' => $this->application->getAccessToken()->getClientToken($useOldToken),
            'appid' => $this->application->getAccount()->getAppId()
        ]);
    }
}