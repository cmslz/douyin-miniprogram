<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:10
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

use Cmslz\DouyinMiniProgram\Kernel\HttpClient\RequestUtil;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;

trait InteractWithServerRequest
{
    protected ?ServerRequestInterface $request = null;

    public function getRequest(): ServerRequestInterface
    {
        if (!$this->request) {
            $this->request = RequestUtil::createDefaultServerRequest();
        }

        return $this->request;
    }

    public function setRequest(ServerRequestInterface $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function setRequestFromSymfonyRequest(Request $symfonyRequest): static
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        $this->request = $psrHttpFactory->createRequest($symfonyRequest);

        return $this;
    }
}