<?php

declare(strict_types=1);

namespace Cmslz\DouyinMiniProgram\Kernel;

use ArrayAccess;
use Cmslz\DouyinMiniProgram\Kernel\Contracts\Jsonable;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\BadRequestException;
use Cmslz\DouyinMiniProgram\Kernel\Support\Xml;
use Cmslz\DouyinMiniProgram\Kernel\Traits\HasAttributes;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @property string $FromUserName
 * @property string $ToUserName
 * @property string $Encrypt
 * @property string $Event
 * @property string $MsgType
 * @implements ArrayAccess<array-key, mixed>
 */
abstract class Message implements ArrayAccess, Jsonable, \JsonSerializable
{
    use HasAttributes;

    /**
     * @param  array<string,string>  $attributes
     */
    final public function __construct(array $attributes = [], protected ?string $originContent = '')
    {
        $this->attributes = $attributes;
    }

    /**
     * @throws BadRequestException
     */
    public static function createFromRequest(ServerRequestInterface $request): Message
    {
        $attributes = self::format($originContent = strval($request->getBody()));

        return new static($attributes, $originContent);
    }

    /**
     * @return array<string,string>
     *
     * @throws BadRequestException
     */
    public static function format(string $originContent): array
    {
        if (0 === stripos($originContent, '<')) {
            $attributes = Xml::parse($originContent);
        }

        // Handle JSON format.
        $dataSet = json_decode($originContent, true);

        if (JSON_ERROR_NONE === json_last_error() && $originContent) {
            $attributes = $dataSet;
        }

        if (empty($attributes) || ! is_array($attributes)) {
            throw new BadRequestException('Failed to decode request contents.');
        }

        return $attributes;
    }

    public function getOriginalContents(): string
    {
        return $this->originContent ?? '';
    }

    public function __toString()
    {
        return $this->toJson() ?: '';
    }
}
