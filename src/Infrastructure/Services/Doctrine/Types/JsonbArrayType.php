<?php

declare(strict_types=1);

namespace App\Infrastructure\Services\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class JsonbArrayType extends JsonType
{

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'JSONB';
    }

    public function getName()
    {
        return 'jsonb';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

}