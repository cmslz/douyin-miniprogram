<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/6/5 下午3:24
 */

namespace Cmslz\DouyinMiniProgram;

use Cmslz\DouyinMiniProgram\Application\PanKnowledge;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\Config as ConfigInterface;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\InvalidClassException;

/**
 * Class Factory
 * @package Cmslz\DouyinMiniProgram
 * Created by xiaobai at 2024/6/5 下午3:25
 * @method static PanKnowledge panKnowledge(array|ConfigInterface $config)
 * @method static Application app(array|ConfigInterface $config)
 */
class Factory
{

    protected static $apps = [
        'app' => Application::class,
    ];
    public static function make($name, array $config)
    {
        $namespace = "\\Cmslz\\DouyinMiniProgram\\Application\\" . ucfirst($name);
        if (!class_exists($namespace)) {
            if (!isset(self::$apps[$name])) {
                throw new InvalidClassException("{$name} not found.");
            }
            return new self::$apps[$name]($config);
        }

        $application = new Application($config);
        return new $namespace($application);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }
}