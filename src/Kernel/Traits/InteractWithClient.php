<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:18
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

use Cmslz\DouyinMiniProgram\AccessToken;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\AccessToken as AccessTokenInterface;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\AccessTokenAwareClient;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\Response;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait InteractWithClient
{
    public function createClient(HttpClientInterface $client, AccessTokenInterface $accessToken): AccessTokenAwareClient
    {
        if ((bool)$this->config->get('http.retry', false)) {
            $client = new RetryableHttpClient(
                $client,
                $this->getRetryStrategy(),
                (int)$this->config->get('http.max_retries', 2) // @phpstan-ignore-line
            );
        }
        return (new AccessTokenAwareClient(
            client: $client,
            accessToken: $accessToken,
            failureJudge: fn(
                Response $response
            ) => (bool)($response->toArray()['errcode'] ?? 0) || !is_null($response->toArray()['error'] ?? null),
            throw: (bool)$this->config->get('http.throw', true),
        ))->setPresets($this->config->all());
    }

    protected function getAccessToken(HttpClientInterface $client)
    {
        return new AccessToken(
            appId: $this->getAccount()->getAppId(),
            secret: $this->getAccount()->getSecret(),
            cache: $this->getCache(),
            httpClient: $client
        );
    }

    abstract public function createOpenClient(): AccessTokenAwareClient;

    abstract public function createTouTiAoClient(): AccessTokenAwareClient;
}