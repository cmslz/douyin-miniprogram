<?php

namespace Cmslz\DouyinMiniProgram\Kernel\Traits;

use Cmslz\DouyinMiniProgram\Kernel\Encryptor;
use Cmslz\DouyinMiniProgram\Kernel\Exceptions\BadRequestException;
use Cmslz\DouyinMiniProgram\Kernel\Message;
use Cmslz\DouyinMiniProgram\Kernel\Support\Xml;

trait DecryptXmlMessage
{
    /**
     * @throws \Cmslz\DouyinMiniProgram\Kernel\Exceptions\RuntimeException
     * @throws BadRequestException
     */
    public function decryptMessage(
        Message $message,
        Encryptor $encryptor,
        string $signature,
        int|string $timestamp,
        string $nonce
    ): Message {
        $ciphertext = $message->Encrypt;

        $this->validateSignature($encryptor->getToken(), $ciphertext, $signature, $timestamp, $nonce);

        $message->merge(Xml::parse(
            $encryptor->decrypt(
                ciphertext: $ciphertext,
                msgSignature: $signature,
                nonce: $nonce,
                timestamp: $timestamp
            )
        ) ?? []);

        return $message;
    }

    /**
     * @throws BadRequestException
     */
    protected function validateSignature(
        string $token,
        string $ciphertext,
        string $signature,
        int|string $timestamp,
        string $nonce
    ): void {
        if (empty($signature)) {
            throw new BadRequestException('Request signature must not be empty.');
        }

        $params = [$token, $timestamp, $nonce, $ciphertext];

        sort($params, SORT_STRING);

        if ($signature !== sha1(implode($params))) {
            throw new BadRequestException('Invalid request signature.');
        }
    }
}
