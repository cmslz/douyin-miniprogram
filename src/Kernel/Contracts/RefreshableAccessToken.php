<?php

namespace Cmslz\DouyinMiniProgram\Kernel\Contracts;

interface RefreshableAccessToken extends AccessToken
{
    public function refreshToken(string $type): string;
}
