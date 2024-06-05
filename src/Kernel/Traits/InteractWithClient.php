<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:18
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

use Cmslz\DouyinMiniProgram\Kernel\HttpClient\AccessTokenAwareClient;

trait InteractWithClient
{
    protected ?AccessTokenAwareClient $client = null;

    public function getClient(): AccessTokenAwareClient
    {
        if (!$this->client) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    public function setClient(AccessTokenAwareClient $client): static
    {
        $this->client = $client;

        return $this;
    }

    abstract public function createClient(): AccessTokenAwareClient;
}