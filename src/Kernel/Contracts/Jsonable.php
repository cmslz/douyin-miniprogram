<?php

declare(strict_types=1);

namespace Cmslz\DouyinMiniProgram\Kernel\Contracts;

interface Jsonable
{
    public function toJson(): string|false;
}
