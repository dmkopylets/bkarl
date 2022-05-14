<?php

declare(strict_types=1);

namespace App\Application\Service\Tokens;

use App\Entity\User\User;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class TokenService
{

    protected JWTTokenManagerInterface $tokenManager;


    protected RefreshTokenManagerInterface $refreshTokenManager;


    protected ValidatorInterface $validator;


    protected string $userIdentityField;


    protected int $ttl;


    public function __construct(
        JWTTokenManagerInterface     $tokenManager,
        RefreshTokenManagerInterface $refreshTokenManager,
        ValidatorInterface           $validator,
        string                       $userIdentityField,
        int                          $ttl
    )
    {
        $this->tokenManager = $tokenManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->validator = $validator;
        $this->userIdentityField = $userIdentityField;
        $this->ttl = $ttl;
    }

    public function getToken(User $user): array
    {
        $token = $this->tokenManager->create($user);

        $datetime = new \DateTime();
        $datetime->modify('+' . $this->ttl . ' seconds');

        $refreshToken = $this->refreshTokenManager->create();

        $accessor = new PropertyAccessor();
        $userIdentityFieldValue = $accessor->getValue($user, $this->userIdentityField);

        $refreshToken->setUsername($userIdentityFieldValue);
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($datetime);

        $valid = false;

        while (false === $valid) {
            $valid = true;
            $errors = $this->validator->validate($refreshToken);
            if ($errors->count() > 0) {
                foreach ($errors as $error) {
                    if ('refreshToken' === $error->getPropertyPath()) {
                        $valid = false;
                        $refreshToken->setRefreshToken();
                    }
                }
            }
        }

        $this->refreshTokenManager->save($refreshToken);

        return [
            'token' => $token,
            'refresh_token' => $refreshToken->getRefreshToken()
        ];
    }
}