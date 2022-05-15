<?php

namespace App\Features\Trip\Repository;

use App\Entity\Trip;
use App\Infrastructure\PaginationRequest\Pagination\Filter;
use App\Infrastructure\PaginationRequest\Pagination\Sort;
use App\Infrastructure\PaginationRequest\PaginationRequestInterface;
use App\Infrastructure\PaginationSerializer\Pagination\ORM\Pagination;
use App\Infrastructure\PaginationSerializer\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\QueryBuilder as QueryBuilderORM;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    protected const DEFAULT_SORT = [
        'COLUMN' => 'id',
        'DIRECTION' => 'ASC'
    ];

    public function __construct(
        ManagerRegistry               $registry,
        public EntityManagerInterface $entityManager,
        public PaginatorInterface     $paginator,
        public Connection             $connection,
        public DenormalizerInterface  $denormalizer
        )
    {
        parent::__construct($registry, Trip::class);
    }

    // /**
    //  * @return Trip[] Returns an array of Trip objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Trip
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function loadById(int $tripId): ?Trip
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT t
                FROM App\Entity\Trip t
                WHERE t.id = :id'
        )
            ->setParameter('id', $tripId)
            ->getOneOrNullResult();
    }

    public function save(Trip $trip): void
    {
        $this->entityManager->persist($trip);
        $this->entityManager->flush();
    }

    public function remove(Trip $trip): void
    {
        $this->entityManager->remove($trip);
        $this->entityManager->flush();
    }

    public function paginate(PaginationRequestInterface $paginationRequest): PaginationInterface
    {

        $stmt = $this->getQuery($paginationRequest, [
            't.id',
            'CONCAT(t.from, \' \',t.to) as marshrut',
            't.driver',
            't.passanger',
            't.date',
            't.vacances',
            't.completed'
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






}
