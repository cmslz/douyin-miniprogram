<?php

namespace Cmslz\DouyinMiniProgram\Kernel;

use ArrayAccess;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\Config as ConfigInterface;
use Cmslz\DouyinMiniProgram\Kernel\Support\Arr;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\InvalidArgumentException;

/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午5:28
 */
class Config implements ArrayAccess, ConfigInterface
{

    /**
     * @var array<string>
     */
    protected array $requiredKeys = [];

    /**
     * @param array<string, mixed> $items
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected array $items = [],
    ) {
        $this->checkMissingKeys();
    }

    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }

    /**
     * @param array<string>|string $key
     */
    public function get(array|string $key, mixed $default = null): mixed
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return Arr::get($this->items, $key, $default);
    }

    /**
     * @param array<string> $keys
     * @return  array<string, mixed>
     */
    public function getMany(array $keys): array
    {
        $config = [];

        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                [$key, $default] = [$default, null];
            }

            $config[$key] = Arr::get($this->items, $key, $default);
        }

        return $config;
    }

    public function set(string $key, mixed $value = null): void
    {
        Arr::set($this->items, $key, $value);
    }

    /**
     * @return  array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function offsetExists(mixed $key): bool
    {
        return $this->has(strval($key));
    }


    public function offsetGet(mixed $key): mixed
    {
        return $this->get(strval($key));
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->set(strval($key), $value);
    }

    public function offsetUnset(mixed $key): void
    {
        $this->set(strval($key), null);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkMissingKeys(): bool
    {
        if (empty($this->requiredKeys)) {
            return true;
        }

        $missingKeys = [];

        foreach ($this->requiredKeys as $key) {
            if (!$this->has($key)) {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            throw new InvalidArgumentException(sprintf("\"%s\" cannot be empty.\r\n", implode(',', $missingKeys)));
        }

        return true;
    }
}