<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/9/12 下午2:38
 */

namespace Cmslz\DouyinMiniProgram\Application;

use Cmslz\DouyinMiniProgram\Application\UserClient\Utils;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithApplication;

class UserClient
{
    use InteractWithApplication;

    public function utils(): Utils
    {
        return new Utils($this->application);
    }
}