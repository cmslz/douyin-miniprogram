<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:18
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

use Cmslz\DouyinMiniProgram\Kernel\HttpClient\AccessTokenAwareClient;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\RequestUtil;
use Cmslz\DouyinMiniProgram\Kernel\Support\Arr;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait InteractWithTouTiAoClient
{
    protected ?AccessTokenAwareClient $touTiAoClient = null;

    public function getTouTiAoClient(): AccessTokenAwareClient
    {
        if (!$this->touTiAoClient) {
            $this->touTiAoClient = $this->createTouTiAoClient();
        }
        return $this->touTiAoClient;
    }

    public function createTouTiAoClient(): AccessTokenAwareClient
    {
        $options = $this->getTouTiAoClientDefaultOptions();
        $optionsByRegexp = Arr::get($options, 'options_by_regexp', []);
        unset($options['options_by_regexp']);
        $client = HttpClient::create(RequestUtil::formatDefaultOptions($options));
        if (!empty($optionsByRegexp)) {
            $client = new ScopingHttpClient($client, $optionsByRegexp);
        }
        $accessToken = $this->getAccessToken($client);
        return $this->createClient($client, $accessToken);
    }

    /**
     * @return array<string,mixed>
     */
    protected function getTouTiAoClientDefaultOptions(): array
    {
        return [];
    }
}