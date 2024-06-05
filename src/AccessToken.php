<?php

declare(strict_types=1);

namespace Cmslz\DouyinMiniProgram;


use Cmslz\DouyinMiniProgram\Kernel\Contracts\RefreshableAccessToken as RefreshableAccessTokenInterface;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\BadResponseException;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\HttpException;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\RequestUtil;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\RequestWithPresets;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\Response;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AccessToken implements RefreshableAccessTokenInterface
{
    use RequestWithPresets;

    const CACHE_KEY_PREFIX = 'mini_account';

    const CACHE_CLIENT_TOKEN = 'client_token_v4';
    const CACHE_CLIENT_OLD_TOKEN = 'client_token_old_v1';

    public function __construct(
        protected string $appId,
        protected string $secret,
        protected CacheInterface $cache,
        protected HttpClientInterface $httpClient,
        protected ?string $key = null
    ) {
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return Response
     * @throws Kernel\Exceptions\InvalidArgumentException
     * @throws TransportExceptionInterface
     * Created by xiaobai at 2024/5/30 下午4:09
     */
    public function request(string $method, string $url, array $options = []): Response
    {
        $options = RequestUtil::formatBody($options);
        return new Response(
            response: $this->httpClient->request($method, ltrim($url, '/'), $options),
        );
    }

    public function getKey(string $tokenType): string
    {
        return $this->key ?? $this->key = sprintf('%s.%s.access_token.%s.%s', static::CACHE_KEY_PREFIX, $tokenType,
            $this->appId,
            $this->secret);
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param bool $oldToken
     * @return string
     * @throws BadResponseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws Kernel\Exceptions\InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function refreshClientToken(bool $oldToken = false): string
    {
        return $oldToken ? $this->getClientAssessTokenOld() : $this->getClientAssessToken();
    }

    /**
     * getAccessToken
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/interface-request-credential/non-user-authorization/get-access-token
     * @return string
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws BadResponseException
     * @throws Kernel\Exceptions\InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getClientAssessTokenOld(): string
    {
        $response = $this->request(
            "POST",
            'api/apps/v2/token',
            [
                'json' => [
                    'grant_type' => 'client_credential',
                    'appid' => $this->appId,
                    'secret' => $this->secret,
                ],
            ]
        );
        if (empty($response['data']['access_token'])) {
            throw new HttpException('Failed to get access_token: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        $this->cache->set($this->getKey(self::CACHE_CLIENT_OLD_TOKEN),
            $response['data']['access_token'],
            intval($response['data']['expires_in']));

        return $response['data']['access_token'];
    }

    /**
     * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/interface-request-credential/non-user-authorization/get-client_token
     * @return string
     * @throws BadResponseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws Kernel\Exceptions\InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getClientAssessToken(): string
    {
        $response = $this->request(
            "POST",
            'oauth/client_token/',
            [
                'json' => [
                    'grant_type' => 'client_credential',
                    'client_key' => $this->appId,
                    'client_secret' => $this->secret,
                ],
            ]
        )->toArray(false);

        if (empty($response['data']['access_token'])) {
            throw new HttpException('Failed to get access_token: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        $this->cache->set($this->getKey(self::CACHE_CLIENT_TOKEN),
            $response['data']['access_token'],
            intval($response['data']['expires_in']));

        return $response['data']['access_token'];
    }

    public function getClientToken(bool $oldToken = false): string
    {
        $token = $this->cache->get($this->getKey($oldToken ? self::CACHE_CLIENT_OLD_TOKEN : self::CACHE_CLIENT_TOKEN));

        if ((bool)$token && is_string($token)) {
            return $token;
        }

        return $this->refreshClientToken($oldToken);
    }

    public function toClientTokenQuery(bool $oldToken = false): array
    {
        return ['access-token' => $this->getClientToken($oldToken)];
    }
}
