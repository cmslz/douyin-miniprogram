<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午5:20
 */

namespace Cmslz\DouyinMiniProgram;

use Cmslz\DouyinMiniProgram\Kernel\Contracts\AccessToken as AccessTokenInterface;
use Cmslz\DouyinMiniProgram\Kernel\Encryptor;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\InvalidConfigException;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\AccessTokenExpiredRetryStrategy;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\RequestUtil;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithCache;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithOpenClient;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithTouTiAoClient;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithConfig;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithClient;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithServerRequest;
use Cmslz\DouyinMiniProgram\Kernel\Traits\LoggerAwareTrait;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\Account as AccountInterface;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\Server as ServerInterface;

class Application
{
    use InteractWithConfig;
    use InteractWithCache;
    use InteractWithServerRequest;
    use InteractWithClient;
    use InteractWithTouTiAoClient;
    use InteractWithOpenClient;
    use LoggerAwareTrait;


    protected ?Encryptor $encryptor = null;

    protected ?ServerInterface $server = null;

    protected ?AccountInterface $account = null;

    protected ?AccessTokenInterface $openAccessToken = null;
    protected ?AccessTokenInterface $touTiAoAccessToken = null;

    public function getEnv(): string
    {
        return strtolower($this->config->get('env', 'prod'));
    }

    public function getAccount(): AccountInterface
    {
        if (!$this->account) {
            $this->account = new Account(
                appId: (string)$this->config->get('appid'), /** @phpstan-ignore-line */
                secret: (string)$this->config->get('secret'), /** @phpstan-ignore-line */
                token: (string)$this->config->get('token'), /** @phpstan-ignore-line */
                aesKey: (string)$this->config->get('aes_key'),/** @phpstan-ignore-line */
            );
        }

        return $this->account;
    }

    public function getTaoTiAoAccessToken(): AccessTokenInterface
    {
        if (!$this->touTiAoAccessToken) {
            $this->touTiAoAccessToken = new AccessToken(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
                cache: $this->getCache(),
                httpClient: $this->getTouTiAoClient()
            );
        }

        return $this->touTiAoAccessToken;
    }

    public function getOpenAccessToken(): AccessTokenInterface
    {
        if (!$this->openAccessToken) {
            $this->openAccessToken = new AccessToken(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
                cache: $this->getCache(),
                httpClient: $this->getOpenClient()
            );
        }

        return $this->openAccessToken;
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

    /**
     * @return array<string,mixed>
     */
    protected function getTouTiAoClientDefaultOptions(): array
    {
        return array_merge(
            ['base_uri' => $this->getEnv() === 'prod' ? 'https://developer.toutiao.com/' : 'https://open-sandbox.douyin.com/'],
            (array)$this->config->get('http', [])
        );
    }

    /**
     * @return array<string,mixed>
     */
    protected function getOpenClientDefaultOptions(): array
    {
        return array_merge(
            ['base_uri' => $this->getEnv() === 'prod' ? 'https://open.douyin.com/' : 'https://open-sandbox.douyin.com/'],
            (array)$this->config->get('http', [])
        );
    }

}