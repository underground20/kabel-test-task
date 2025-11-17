<?php

namespace App\Infrastructure\Persistence;

use App\App\Import\Model\Import;
use App\App\Import\Model\ImportType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ImportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Import::class);
    }

    public function findLastByType(ImportType $type): ?Import
    {
        return $this->createQueryBuilder('i')
            ->where('i.type = :type')
            ->setParameter('type', $type->value)
            ->orderBy('i.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
