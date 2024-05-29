<?php

namespace Cmslz\DouyinMiniProgram;

/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午5:35
 */
class Config extends Kernel\Config
{
    protected array $requiredKeys = [
        'appid',
        'secret',
//        'private_key',
//        'public_key',
    ];
}