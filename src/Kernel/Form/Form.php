<?php

namespace Cmslz\DouyinMiniProgram\Kernel\Form;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class Form
{
    /**
     * @param  array<string|array|DataPart>  $fields
     */
    public function __construct(protected array $fields)
    {
    }

    /**
     * @param  array<string|array|DataPart>  $fields
     */
    public static function create(array $fields): Form
    {
        return new self($fields);
    }

    /**
     * @return  array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->toOptions();
    }

    /**
     * @return array<string,mixed>
     */
    public function toOptions(): array
    {
        $formData = new FormDataPart($this->fields);

        return [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToString(),
        ];
    }
}
