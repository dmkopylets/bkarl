<?php

declare(strict_types=1);

namespace App\Features\User\Repository;

use App\Entity\User\User;
use App\Infrastructure\PaginationRequest\Pagination\Filter;
use App\Infrastructure\PaginationRequest\Pagination\Sort;
use App\Infrastructure\PaginationRequest\PaginationRequestInterface;
use App\Infrastructure\PaginationSerializer\Pagination\ORM\Pagination;
use App\Infrastructure\PaginationSerializer\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\QueryBuilder as QueryBuilderORM;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    protected const DEFAULT_SORT = [
        'COLUMN' => 'id',
        'DIRECTION' => 'ASC'
    ];

    private const  STATUSES = [
        'ACTIVE' => 10,
        'INVITED' => 5,
        'PENDING' => 3,
        'MAIL_CONFIRMED' => 2,
        'BLOCKED' => 1,
        'DELETED' => 0
    ];

    public function __construct(
        ManagerRegistry               $registry,
        public EntityManagerInterface $entityManager,
        public PaginatorInterface     $paginator,
        public Connection             $connection,
        public DenormalizerInterface  $denormalizer
    )
    {
        parent::__construct($registry, User::class);
    }


    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function loadUserByIdentifier(string $usernameOrEmail): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\User\User u
                WHERE CONCAT(u.firstName, u.lastName) = :query
                OR u.email = :query'
        )
            ->setParameter('query', $usernameOrEmail)
            ->getOneOrNullResult();
    }

    public function loadByPhone(string $phone): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\User\User u
                WHERE u.phone = :phone'
        )
            ->setParameter('phone', $phone)
            ->getOneOrNullResult();
    }

    public function loadByChangePhone(string $changePhone): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\User\User u
                WHERE u.changedPhone = :phone'
        )
            ->setParameter('phone', $changePhone)
            ->getOneOrNullResult();
    }

    public function loadById(int $userId): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\User\User u
                WHERE u.id = :id'
        )
            ->setParameter('id', $userId)
            ->getOneOrNullResult();
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function remove(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function paginate(PaginationRequestInterface $paginationRequest): PaginationInterface
    {

        $stmt = $this->getQuery($paginationRequest, [
            'u.id',
            'CONCAT(u.first_name, \' \',u.last_name) as username',
            'u.phone',
            'u.status',
            'u.created_at',
            'u.description',
            'u.car'
        ]);
        return new Pagination($this->paginator->paginate(
            $stmt,
            $paginationRequest->getPagination()->getPage(),
            $paginationRequest->getPagination()->getSize()
        ));
    }

    public function getQuery(PaginationRequestInterface $paginationRequest, array $select): QueryBuilder
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select($select)
            ->from('"user"', 'u');
        $this->setFilters($stmt, $paginationRequest->getFilter());
        $this->setSort($stmt, $paginationRequest->getSort());

        return $stmt;
    }


    private function setFilters(QueryBuilder $queryBuilder, Filter $filters): void
    {
        $filter = $filters->getFilters();

        if ((isset($filter['status']))) {
            $queryBuilder->andWhere("u.status = :status");
            $queryBuilder->setParameter(":status", $filter['status']);
        } else {
            $queryBuilder->andWhere("u.status = " . self::STATUSES['ACTIVE']);
        }

        if (isset($filter['username'])) {
            $queryBuilder->andWhere("CONCAT(u.first_name, u.last_name) LIKE LOWER(:name)");
            $queryBuilder->setParameter(":name", '%' . $filter['username'] . '%');
        }

        if (isset($filter['email'])) {
            $queryBuilder->andWhere("LOWER(u.email) LIKE LOWER(:email)");
            $queryBuilder->setParameter(":email", '%' . $filter['email'] . '%');
        }

        if (isset($filter['id'])) {
            $queryBuilder->andWhere("u.id = :id");
            $queryBuilder->setParameter(":id", $filter['id']);
        }

        if (isset($filter['phone'])) {
            $queryBuilder->andWhere("u.phone LIKE :phone");
            $queryBuilder->setParameter(":phone", '%' . $filter['phone'] . '%');
        }

    }

    protected function setSort(QueryBuilder $queryBuilder, Sort $sort): void
    {
        $column = $sort->getColumn() ?: static::DEFAULT_SORT['COLUMN'];
        $direction = $sort->getDirection() ?: static::DEFAULT_SORT['DIRECTION'];

        if ($column && $direction) {
            $queryBuilder->orderBy($column, $direction);
        }
    }

    public function paginateContacts(PaginationRequestInterface $paginationRequest): PaginationInterface
    {

        $filter = $paginationRequest->getFilter()->getFilters();

        $contacts = $this->createQueryBuilder('u')
            ->select([
                'u.id',
                "concat(u.firstName, ' ', u.lastName)as username",
                'u.credentials',
                "JSON_AGG(f.id) as facilityId",
                '(f.name) as facilityName'
            ])
            ->leftJoin('u.invitedBy', 'fu')
            ->leftJoin('fu.facility', 'f')
            ->where('fu.facility IN (:facilityIds) AND u.id <> :userId')
            ->setParameter('facilityIds', $filter['facilityId'], Connection::PARAM_STR_ARRAY)
            ->setParameter('userId', $filter['userId'])
            ->orderBy('f.name')
            ->groupBy('u.id, f.name');

        if (isset($filter['searchWord'])) {
            // $contacts->andWhere('u.credentials = ' . User::CREDENTIALS['MD'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['MD']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['DO'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['DO']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['MBBS'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['MBBS']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['MBBCh'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['MBBCh']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['NP'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['NP']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['CNP'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['CNP']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['APNP'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['APNP']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['PA'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['PA']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['RN'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['RN']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['APRN'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['APRN']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['DNP'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['DNP']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['LPN'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['LPN']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere('u.credentials = ' . User::CREDENTIALS['CNA'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['CNA']] . '\') LIKE LOWER(:searchWord)');
            // $contacts->orWhere("LOWER(concat(u.firstName, ' ', u.lastName)) LIKE LOWER(:searchWord)");
            $contacts->setParameter('searchWord', '%' . $filter['searchWord'] . '%');
        }

        return new Pagination($this->paginator->paginate(
            $contacts,
            $paginationRequest->getPagination()->getPage(),
            $paginationRequest->getPagination()->getSize()
        ));
    }

    public function loadProviders(PaginationRequestInterface $paginationRequest): PaginationInterface
    {

        $filter = $paginationRequest->getFilter()->getFilters();

        $providers = $this->createQueryBuilder('u')
            ->select([
                'u.id',
                "concat(u.firstName, ' ', u.lastName)as name",
                'u.credentials',
                "JSON_AGG(f.id) as facilityId",
                'fu.status'
            ])
            ->leftJoin('u.invitedBy', 'fu')
            ->leftJoin('fu.facility', 'f')
            ->where ('JSONB_AG(u.roles, \'["'.User::ROLES['PROVIDER'].'"]\') = TRUE')
            ->groupBy('u.id, fu.status');


        if (isset($filter['searchWord'])) {
//            $providers->andWhere('u.credentials = ' . User::CREDENTIALS['MD'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['MD']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['DO'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['DO']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['MBBS'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['MBBS']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['MBBCh'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['MBBCh']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['NP'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['NP']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['CNP'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['CNP']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['APNP'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['APNP']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['PA'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['PA']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['RN'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['RN']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['APRN'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['APRN']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['DNP'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['DNP']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['LPN'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['LPN']] . '\') LIKE LOWER(:searchWord)');
//            $providers->orWhere('u.credentials = ' . User::CREDENTIALS['CNA'] . ' AND LOWER(\'' . User::CREDENTIALS_TITLE[User::CREDENTIALS['CNA']] . '\') LIKE LOWER(:searchWord)');
            $providers->andWhere("LOWER(concat(u.firstName, ' ', u.lastName)) LIKE LOWER(:searchWord)");
            $providers->setParameter('searchWord', '%' . $filter['searchWord'] . '%');
        }

        if (isset($filter['status'])) {
            $providers->andWhere("u.status = :status)");
            $providers->setParameter('status', $filter['status']);
        }

        if (isset($filter['credentials'])) {
            $providers->andWhere("u.credentials = LOWER(:credentials)");
            $providers->setParameter('credentials', $filter['credentials']);
        }

        return new Pagination($this->paginator->paginate(
            $providers,
            $paginationRequest->getPagination()->getPage(),
            $paginationRequest->getPagination()->getSize()
        ));
    }

    public function isChangedPhoneNotUnique(string $phone): bool
    {
        return (bool)$this->createQueryBuilder('u')
            ->andWhere('u.phone = :phone')
            ->setParameter('phone', '' . $phone . '')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
