<?php

declare(strict_types=1);

namespace Cmslz\DouyinMiniProgram\Kernel\Contracts;

interface Account
{
    public function getAppId(): string;

    public function getSecret(): string;

    public function getToken(): ?string;

    public function getAesKey(): ?string;
}
