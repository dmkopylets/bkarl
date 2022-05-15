<?php

declare(strict_types=1);

namespace App\Features\Trip\Service;

use App\Features\Trip\Repository\TripRepository;
use App\Entity\Trip;
use App\Infrastructure\PaginationRequest\PaginationRequestInterface;
use App\Infrastructure\PaginationSerializer\Pagination\PaginationInterface;


class TripService
{
    public function __construct(public TripRepository $tripRepository) {}

    public function findTripByDate(string $date): ?Trip
    {
        return $this->tripRepository->loadTripByIdentifier($date);
    }

    public function findById(int $tripId): ?Trip
    {
        return $this->tripRepository->loadById($tripId);
    }
    
    public function findByFrom(string $from): ?Trip
    {
        return $this->tripRepository->loadByFrom($from);
    }

    public function findByTo(string $to): ?Trip
    {
        return $this->tripRepository->loadByTo($to);
    }

    public function getByFrom(string $from): ?Trip
    {
        if(!$trip = $this->findByFrom($from)){
            //throw new TripNotFoundException();
        }
        return $trip;
    }

    public function getByTo(string $to): ?Trip
    {
        if(!$trip = $this->findByTo($to)){
            //throw new TripNotFoundException();
        }
        return $trip;
    }

    public function getById(int $tripId): ?Trip
    {
        if(!$trip = $this->findById($tripId)){
            //throw new TripNotFoundException('this tripId does`t exist');
        }

        return $trip;
    }

    
    public function getContacts(PaginationRequestInterface $paginationRequest): PaginationInterface
    {
        return $this->tripRepository->paginateContacts($paginationRequest);
    }

    public function getItems(PaginationRequestInterface $paginationRequest): PaginationInterface
    {
        return $this->tripRepository->paginate($paginationRequest);
    }

    public function getTableColumns(): array
    {
        return [
            'id',
            'marshrut',
            'driver',
            'passanger',
            'date',
            'vacances',
            'completed',
            'created_at'
        ];
    }

    public function save(Trip $trip): void
    {
        $this->tripRepository->save($trip);
    }



    public function getProviders(PaginationRequestInterface $paginationRequest): PaginationInterface
    {
        return $this->tripRepository->loadProviders($paginationRequest);
    }

    public function isNotUniquePhone($changePhone): bool
    {
        return $this->tripRepository->isChangedPhoneNotUnique($changePhone);
    }
}
