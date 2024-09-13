<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:19
 */

namespace Cmslz\DouyinMiniProgram\Kernel\HttpClient;

use Closure;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\AccessToken as AccessTokenInterface;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\AccessTokenAwareHttpClient as AccessTokenAwareHttpClientInterface;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\InvalidArgumentException;
use Cmslz\DouyinMiniProgram\Kernel\Traits\MockableHttpClient;
use Symfony\Component\HttpClient\AsyncDecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AccessTokenAwareClient
 * @method HttpClientInterface withAppIdAs(null|string $name = null) 自定义 app_id 参数名
 * @method HttpClientInterface withAppId(null|string $value = null)
 * @package Cmslz\DouyinMiniProgram\Kernel\HttpClient
 * Created by xiaobai at 2024/5/29 下午6:48
 */
class AccessTokenAwareClient implements AccessTokenAwareHttpClientInterface
{
    use AsyncDecoratorTrait;
    use HttpClientMethods;
    use RetryableClient;
    use MockableHttpClient;
    use RequestWithPresets;

    public function __construct(
        HttpClientInterface $client = null,
        protected ?AccessTokenInterface $accessToken = null,
        protected ?Closure $failureJudge = null,
        protected bool $throw = true
    ) {
        $this->client = $client ?? HttpClient::create();
    }

    public function withAccessToken(AccessTokenInterface $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array<string, mixed> $options
     * @param bool $oldToken
     * @return Response
     * @throws InvalidArgumentException
     */
    public function request(string $method, string $url, array $options = [], bool $oldToken = false): Response
    {
        $headers = $options['headers'] ?? [];
        if ($this->accessToken) {
            $headers = array_merge($headers, $this->accessToken->toClientTokenQuery($oldToken));
        }
        $options['headers'] = $headers;
        $options = RequestUtil::formatBody($this->mergeThenResetPrepends($options));
        return $this->requestCustom($method, $url, $options);
    }

    public function requestCustom(string $method, string $url, array $options = []): Response
    {
        return new Response(
            response: $this->client->request($method, ltrim($url, '/'), $options),
            failureJudge: $this->failureJudge,
            throw: $this->throw
        );
    }

    /**
     * @param array<int, mixed> $arguments
     * @throws InvalidArgumentException
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (\str_starts_with($name, 'with')) {
            return $this->handleMagicWithCall($name, $arguments[0] ?? null);
        }

        return $this->client->$name(...$arguments);
    }

    public static function createMockClient(MockHttpClient $mockHttpClient): HttpClientInterface
    {
        return new self($mockHttpClient);
    }
}