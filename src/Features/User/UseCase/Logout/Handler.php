<?php

declare(strict_types=1);

namespace App\Features\User\UseCase\Logout;

use Doctrine\DBAL\Connection;

class Handler
{
    public function __construct(public Connection $databaseConnection) {}

    public function handle(Command $command): void
    {
        $this->databaseConnection->executeStatement(
            'DELETE FROM refresh_tokens WHERE username=:username', ['username' => $command->phone]
        );
    }
}