<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午5:20
 */

namespace Cmslz\DouyinMiniProgram;

use Cmslz\DouyinMiniProgram\Kernel\Contracts\AccessToken as AccessTokenInterface;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\AccessTokenAwareClient;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\AccessTokenExpiredRetryStrategy;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\RequestUtil;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\Response;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithCache;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithClient;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithConfig;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithHttpClient;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithServerRequest;
use Cmslz\DouyinMiniProgram\Kernel\Traits\LoggerAwareTrait;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\Account as AccountInterface;

class Application
{
    use InteractWithConfig;
    use InteractWithCache;
    use InteractWithServerRequest;
    use InteractWithHttpClient;
    use InteractWithClient;
    use LoggerAwareTrait;


    protected ?AccountInterface $account = null;

    protected ?AccessTokenInterface $accessToken = null;

    public function getAccount(): AccountInterface
    {
        if (!$this->account) {
            $this->account = new Account(
                appId: (string)$this->config->get('app_id'), /** @phpstan-ignore-line */
                secret: (string)$this->config->get('secret'), /** @phpstan-ignore-line */
                token: (string)$this->config->get('token'), /** @phpstan-ignore-line */
                aesKey: (string)$this->config->get('aes_key'),/** @phpstan-ignore-line */
            );
        }

        return $this->account;
    }

    public function createClient(): AccessTokenAwareClient
    {
        $httpClient = $this->getHttpClient();

        if ((bool)$this->config->get('http.retry', false)) {
            $httpClient = new RetryableHttpClient(
                $httpClient,
                $this->getRetryStrategy(),
                (int)$this->config->get('http.max_retries', 2) // @phpstan-ignore-line
            );
        }

        return (new AccessTokenAwareClient(
            client: $httpClient,
            accessToken: $this->getAccessToken(),
            failureJudge: fn(
                Response $response
            ) => (bool)($response->toArray()['errcode'] ?? 0) || !is_null($response->toArray()['error'] ?? null),
            throw: (bool)$this->config->get('http.throw', true),
        ))->setPresets($this->config->all());
    }

    public function getAccessToken(): AccessTokenInterface
    {
        if (!$this->accessToken) {
            $this->accessToken = new AccessToken(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
                cache: $this->getCache(),
                httpClient: $this->getHttpClient(),
                stable: $this->config->get('use_stable_access_token', false)
            );
        }

        return $this->accessToken;
    }

    public function getRetryStrategy(): AccessTokenExpiredRetryStrategy
    {
        $retryConfig = RequestUtil::mergeDefaultRetryOptions((array)$this->config->get('http.retry', []));

        return (new AccessTokenExpiredRetryStrategy($retryConfig))
            ->decideUsing(function (AsyncContext $context, ?string $responseContent): bool {
                return !empty($responseContent)
                    && str_contains($responseContent, '42001')
                    && str_contains($responseContent, 'access_token expired');
            });
    }

}