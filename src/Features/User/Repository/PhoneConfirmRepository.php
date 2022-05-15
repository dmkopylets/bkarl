<?php

namespace App\Features\User\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use App\Entity\User\PhoneConfirm;
use Doctrine\DBAL\Connection;

class PhoneConfirmRepository extends ServiceEntityRepository
{
    private const PIN_ALIVE = '- 8 hours';
    private const PIN_RESEND_WAIT_TIME = '- 1 minute';

    public function __construct(ManagerRegistry $registry, public Connection $connection, public EntityManagerInterface $entityManager,)
    {
        parent::__construct($registry, PhoneConfirm::class);
    }

    public function getValidPin(string $pin, string $phone = '',string $changePhone = ''): ?PhoneConfirm
    {
        $approveTime = (new \DateTime(self::PIN_ALIVE));
        $query = $this->createQueryBuilder('pc')
            ->andWhere('pc.createdAt >= :createdAt')
            ->setParameter('createdAt', $approveTime->format('Y-m-d H:i:s'))
            ->andWhere('pc.pin = :pin')
            ->setMaxResults(1)
            ->orderBy('pc.id', "DESC")
            ->setParameter('pin', $pin);

        if($phone){
            $query->leftJoin('pc.user', 'u')
                  ->andWhere('u.phone = :phone')
                  ->setParameter('phone', $phone);
        }
        if($changePhone){
            $query->leftJoin('pc.user', 'u')
                  ->andWhere('u.changedPhone = :changed_phone')
                  ->setParameter('changed_phone', $changePhone);
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    public function hasSendedPin($phone): bool
    {
        $resendTime = (new \DateTime(self::PIN_RESEND_WAIT_TIME));
        return (bool)$this->createQueryBuilder('pc')
            ->leftJoin('pc.user', 'u')
            ->where('pc.createdAt >= :created_at')
            ->setParameter('created_at', $resendTime->format('Y-m-d H:i:s'))
            ->andWhere('u.phone = :phone')
            ->setParameter('phone', $phone)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function hasSendedPinChangePhone($changePhone): bool
    {
        $resendTime = (new \DateTime(self::PIN_RESEND_WAIT_TIME));
        return (bool)$this->createQueryBuilder('pc')
            ->leftJoin('pc.user', 'u')
            ->where('pc.createdAt >= :created_at')
            ->setParameter('created_at', $resendTime->format('Y-m-d H:i:s'))
            ->andWhere('u.changedPhone = :changed_phone')
            ->setParameter('changed_phone', $changePhone)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadPhoneConfirmByIdentifier(string $userId): ?PhoneConfirm
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT pc
                FROM App\Entity\User\PhoneConfirm pc
                WHERE pc.user_id = :query'
        )
            ->setParameter('query', $userId)
            ->getOneOrNullResult();
    }

    public function getQuery(array $select): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
                    ->select($select)
                    ->from('"phoneConfirm"', 'pc');
    }

    public function save(PhoneConfirm $phoneConfirm): void
    {
        $this->entityManager->persist($phoneConfirm);
        $this->entityManager->flush();
    }
}