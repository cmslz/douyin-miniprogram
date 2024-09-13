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

trait InteractWithOpenClient
{
    protected ?AccessTokenAwareClient $openClient = null;

    public function getOpenClient(): AccessTokenAwareClient
    {
        if (!$this->openClient) {
            $this->openClient = $this->createOpenClient();
        }
        return $this->openClient;
    }

    public function createOpenClient(): AccessTokenAwareClient
    {
        $options = $this->getOpenClientDefaultOptions();
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
    protected function getOpenClientDefaultOptions(): array
    {
        return [];
    }
}