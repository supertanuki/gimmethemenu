<?php

namespace Application\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ReviewRepository extends EntityRepository
{
    private function commonQuery()
    {
        return $this->createQueryBuilder('review');
    }

    public function getLatest()
    {
        return $this->commonQuery()
            ->where('review.photoName IS NOT NULL')
            ->orderBy('review.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }
}
