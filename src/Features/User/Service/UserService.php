<?php

declare(strict_types=1);

namespace App\Features\User\Service;

use App\Features\User\Repository\UserRepository;
use App\Entity\User\User;
use App\Infrastructure\PaginationRequest\PaginationRequestInterface;
use App\Infrastructure\PaginationSerializer\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserService
{
    public function __construct(public UserRepository $userRepository) {}

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->loadUserByIdentifier($email);
    }

    public function getByEmail(string $email): ?User
    {
        if(!$user = $this->findUserByEmail($email)){
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    public function findByPhone(string $phone): ?User
    {
        return $this->userRepository->loadByPhone($phone);
    }

    public function findByChangedPhone(string $changePhone): ?User
    {
        return $this->userRepository->loadByChangePhone($changePhone);
    }

    public function getByPhone(string $phone): ?User
    {
        if(!$user = $this->findByPhone($phone)){
            throw new UserNotFoundException('Not valid phone');
        }

        return $user;
    }

    public function findById(int $userId): ?User
    {
        return $this->userRepository->loadById($userId);
    }

    public function getById(int $userId): ?User
    {
        if(!$user = $this->findById($userId)){
            throw new UserNotFoundException('this userId does`t exist');
        }

        return $user;
    }

    public function getByChangedPhone(string $changePhone): ?User
    {
        if(!$user = $this->findByChangedPhone($changePhone)){
            throw new UserNotFoundException('Not valid phone');
        }

        return $user;
    }

    public function getContacts(PaginationRequestInterface $paginationRequest): PaginationInterface
    {
        return $this->userRepository->paginateContacts($paginationRequest);
    }

    public function getItems(PaginationRequestInterface $paginationRequest): PaginationInterface
    {
        return $this->userRepository->paginate($paginationRequest);
    }

    public function getTableColumns(): array
    {
        return [
            'id',
            'username',
            'phone',
            'status',
            'description',
            'car',
            'created_at'
        ];
    }

    public function getProviders(PaginationRequestInterface $paginationRequest): PaginationInterface
    {
        return $this->userRepository->loadProviders($paginationRequest);
    }

    public function isNotUniquePhone($changePhone): bool
    {
        return $this->userRepository->isChangedPhoneNotUnique($changePhone);
    }
}
