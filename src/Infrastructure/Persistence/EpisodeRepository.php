<?php

namespace App\Infrastructure\Persistence;

use App\Api\DTO\EpisodeFilter;
use App\Domain\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

final class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    public function getQuery(EpisodeFilter $filter): Query
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.season')
        ;

        if (null !== $filter->season) {
            $qb->where('e.season = :season');
            $qb->setParameter('season', $filter->season);
        }

        if (null !== $filter->from) {
            $qb->andWhere('e.releaseDate >= :from');
            $qb->setParameter('from', $filter->from);
        }

        if (null !== $filter->to) {
            $qb->andWhere('e.releaseDate < :to');
            $qb->setParameter('to', $filter->to);
        }

        return $qb->getQuery();
    }
}
