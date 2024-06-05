<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/6/5 下午3:29
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

use Cmslz\DouyinMiniProgram\Application;
use Cmslz\DouyinMiniProgram\Kernel\HttpClient\Response;

trait InteractWithApplication
{
    public function __construct(protected Application $application)
    {
    }

    public function result(Response $response)
    {
        return $response->toArray();
    }
}