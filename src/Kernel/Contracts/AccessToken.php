<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:20
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Contracts;

interface AccessToken
{
    public function getToken(): string;

    /**
     * @return array<string,string>
     */
    public function toQuery(): array;
}