<?php
/**
 * Each engineer has a duty to keep the code elegant
 * Created by xiaobai at 2024/5/29 下午6:15
 */

namespace Cmslz\DouyinMiniProgram\Kernel\Support;

use TheNorthMemory\Xml\Transformer;

class Xml
{
    public static function parse(string $xml): ?array
    {
        return Transformer::toArray($xml);
    }

    public static function build(array $data): string
    {
        return Transformer::toXml($data);
    }
}
