<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/6/6 上午11:11
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

trait InteractWithMergeAppidAndAccessToken
{
    private function mergeAppidAndToken(array $data = [], bool $useOldToken = true, array $columns = []): array
    {
        $accessTokenColumn = $columns['access_token'] ?? 'access_token';
        $appidColumn = $columns['appid'] ?? 'appid';
        return array_merge($data, [
            $accessTokenColumn => $this->application->getAccessToken()->getClientToken($useOldToken),
            $appidColumn => $this->application->getAccount()->getAppId()
        ]);
    }
}