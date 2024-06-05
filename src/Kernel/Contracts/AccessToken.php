<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:20
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Contracts;

interface AccessToken
{
    /**
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/interface-request-credential/non-user-authorization/get-client_token
     * @return string
     * Created by xiaobai at 2024/5/30 下午4:36
     */
    public function getClientToken(): string;

    /**
     * @return array<string,string>
     */
    public function toClientTokenQuery(): array;
}