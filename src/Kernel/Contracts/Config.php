<?php

namespace Cmslz\DouyinMiniProgram\Kernel\Contracts;

use ArrayAccess;

/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午5:22
 */
interface Config extends ArrayAccess
{
    /**
     * @return array<string,mixed>
     */
    public function all(): array;

    public function has(string $key): bool;

    public function set(string $key, mixed $value = null): void;

    /**
     * @param  array<string>|string  $key
     */
    public function get(array|string $key, mixed $default = null): mixed;
}