<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/9/12 下午4:03
 */

namespace Cmslz\DouyinMiniProgram\Application;

use Cmslz\DouyinMiniProgram\Application\Share\Schema;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithApplication;

class Share
{
    use InteractWithApplication;

    public function schema(): Schema
    {
        return new Schema($this->application);
    }
}