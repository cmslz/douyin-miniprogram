<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:32
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

use Mockery\Mock;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait MockableHttpClient
{
    public static function createMockClient(MockHttpClient $mockHttpClient): HttpClientInterface
    {
        return new self($mockHttpClient);
    }

    /**
     * @param array<string,mixed> $headers
     */
    public static function mock(
        string $response = '',
        ?int $status = 200,
        array $headers = [],
        string $baseUri = 'https://example.com'
    ): object {
        $mockResponse = new MockResponse(
            $response,
            array_merge([
                'http_code' => $status,
                'content_type' => 'application/json',
            ], $headers)
        );

        $client = self::createMockClient(new MockHttpClient($mockResponse, $baseUri));

        // @phpstan-ignore-next-line
        return new class($client, $mockResponse) {
            use DecoratorTrait;

            public function __construct(Mock|HttpClientInterface $client, public MockResponse $mockResponse)
            {
                $this->client = $client;
            }

            /**
             * @param array<string,mixed> $arguments
             */
            public function __call(string $name, array $arguments): mixed
            {
                return $this->client->$name(...$arguments);
            }

            public function getRequestMethod(): string
            {
                return $this->mockResponse->getRequestMethod();
            }

            public function getRequestUrl(): string
            {
                return $this->mockResponse->getRequestUrl();
            }

            /**
             * @return array<string, mixed>
             */
            public function getRequestOptions(): array
            {
                return $this->mockResponse->getRequestOptions();
            }
        };
    }
}