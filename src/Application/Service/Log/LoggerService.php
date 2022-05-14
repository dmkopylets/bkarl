<?php

declare(strict_types=1);

namespace App\Application\Service\Log;

use Psr\Log\LoggerInterface;

class LoggerService
{
    public function __construct(public LoggerInterface $infoLogger) {}

    public function error(string $message, array $data = []): void
    {
        $this->infoLogger->error(sprintf($message, ...$data));
    }

    public function info(string $message, array $data = []): void
    {
        $this->infoLogger->info(sprintf($message, ...$data));
    }
}