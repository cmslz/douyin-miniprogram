<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/6/5 下午3:28
 */

namespace Cmslz\DouyinMiniProgram\Application;

use Cmslz\DouyinMiniProgram\Application;
use Cmslz\DouyinMiniProgram\Application\PanKnowledge\Course;
use Cmslz\DouyinMiniProgram\Application\PanKnowledge\Role;
use Cmslz\DouyinMiniProgram\Kernel\Traits\InteractWithApplication;

/**
 * 泛知识
 * @link https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/pan-knowledge
 * Class PanKnowledge
 * @package Cmslz\DouyinMiniProgram\Application
 * Created by xiaobai at 2024/6/5 下午3:30
 */
class PanKnowledge
{
    use InteractWithApplication;

    public function role(): Role
    {
        return new Role($this->application);
    }

    public function course(): Course
    {
        return new Course($this->application);
    }

    public function app(): Application
    {
        return $this->application;
    }
}