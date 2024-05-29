<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:13
 */

namespace Cmslz\DouyinMiniProgram\Kernel\HttpClient;

use Cmslz\DouyinMiniProgram\Kernel\Exceptions\InvalidArgumentException;
use Cmslz\DouyinMiniProgram\Kernel\Support\Xml;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestUtil
{
    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public static function mergeDefaultRetryOptions(array $options): array
    {
        return \array_merge([
            'status_codes' => GenericRetryStrategy::DEFAULT_RETRY_STATUS_CODES,
            'delay' => 1000,
            'max_delay' => 0,
            'max_retries' => 3,
            'multiplier' => 2.0,
            'jitter' => 0.1,
        ], $options);
    }

    /**
     * @param array<string, array|mixed> $options
     * @return array<string, array|mixed>
     */
    public static function formatDefaultOptions(array $options): array
    {
        return \array_filter(
            array: $options,
            callback: fn($key) => array_key_exists($key, HttpClientInterface::OPTIONS_DEFAULTS),
            mode: ARRAY_FILTER_USE_KEY
        );
    }

    public static function formatOptions(array $options, string $method): array
    {
        if (array_key_exists('query', $options) && is_array($options['query']) && empty($options['query'])) {
            return $options;
        }

        if (array_key_exists('body', $options)
            || array_key_exists('json', $options)
            || array_key_exists('xml', $options)
        ) {
            return $options;
        }

        $contentType = $options['headers']['Content-Type'] ?? $options['headers']['content-type'] ?? null;
        $name = in_array($method, ['GET', 'HEAD', 'DELETE']) ? 'query' : 'body';

        if ($contentType === 'application/json') {
            $name = 'json';
        }

        if ($contentType === 'text/xml') {
            $name = 'xml';
        }

        foreach ($options as $key => $value) {
            if (!array_key_exists($key, HttpClientInterface::OPTIONS_DEFAULTS)) {
                $options[$name][trim($key, '"')] = $value;
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * @param array<string, array<string,mixed>|mixed> $options
     * @return array<string, array|mixed>
     * @throws InvalidArgumentException
     */
    public static function formatBody(array $options): array
    {
        $contentType = $options['headers']['Content-Type'] ?? $options['headers']['content-type'] ?? null;

        if (isset($options['xml'])) {
            if (is_array($options['xml'])) {
                $options['xml'] = Xml::build($options['xml']);
            }

            if (!is_string($options['xml'])) {
                throw new InvalidArgumentException('The type of `xml` must be string or array.');
            }

            if (!$contentType) {
                $options['headers']['Content-Type'] = [$options['headers'][] = 'Content-Type: text/xml'];
            }

            $options['body'] = $options['xml'];
            unset($options['xml']);
        }

        if (isset($options['json'])) {
            if (is_array($options['json'])) {
                $options['json'] = json_encode(
                    $options['json'],
                    empty($options['json']) ? JSON_FORCE_OBJECT : JSON_UNESCAPED_UNICODE
                );
            }

            if (!is_string($options['json'])) {
                throw new InvalidArgumentException('The type of `json` must be string or array.');
            }

            if (!$contentType) {
                $options['headers']['Content-Type'] = [$options['headers'][] = 'Content-Type: application/json'];
            }

            $options['body'] = $options['json'];
            unset($options['json']);
        }

        return $options;
    }

    public static function createDefaultServerRequest(): ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();

        $creator = new ServerRequestCreator(
            serverRequestFactory: $psr17Factory,
            uriFactory: $psr17Factory,
            uploadedFileFactory: $psr17Factory,
            streamFactory: $psr17Factory
        );

        return $creator->fromGlobals();
    }
}