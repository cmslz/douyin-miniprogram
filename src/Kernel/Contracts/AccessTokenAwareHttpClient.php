<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:19
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Contracts;


use Symfony\Contracts\HttpClient\HttpClientInterface;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\AccessToken as AccessTokenInterface;

interface AccessTokenAwareHttpClient extends HttpClientInterface
{
    public function withAccessToken(AccessTokenInterface $accessToken): static;
}