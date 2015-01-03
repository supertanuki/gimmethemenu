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

    public function countReviews($dish, $userToAvoid=null)
    {
        return $this->commonQuery()
            ->select('COUNT(review.id)')
            ->where('review.user != :user')
            ->setParameter('user', $userToAvoid)
            ->andWhere('review.dish = :dish')
            ->setParameter('dish', $dish)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getLatest()
    {
        return $this->commonQuery()
            ->where('review.photoName IS NOT NULL')
            ->join('review.dish', 'd')
            ->join('review.user', 'u')
            ->join('d.restaurant', 'r')
            ->join('r.locality', 'l')
            ->join('r.country', 'c')
            ->addSelect(array('d', 'u', 'r', 'l', 'c'))
            ->orderBy('review.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    public function getLatestForUser($user)
    {
        return $this->commonQuery()
            ->join('review.dish', 'd')
            ->where('review.user = :user')
            ->setParameter('user', $user)
            ->orderBy('review.when', 'DESC')
            ->join('d.restaurant', 'r')
            ->join('r.locality', 'l')
            ->join('r.country', 'c')
            ->select(array('review', 'd', 'r', 'l', 'c'))
            ->groupBy('r.id')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getReviewsFromFollowed($user)
    {
        return $this->commonQuery()
            ->join('review.dish', 'd')
            ->join('review.user', 'u')
            ->join('u.followers', 'f')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('review.when', 'DESC')
            ->addOrderBy('review.createdAt', 'DESC')
            ->join('d.restaurant', 'r')
            ->join('r.locality', 'l')
            ->join('r.country', 'c')
            ->select(array('review', 'f', 'd', 'u', 'r', 'l', 'c'))
            ->getQuery()
            ->getResult();
    }
}
