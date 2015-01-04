<?php

namespace Application\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    private function commonQuery()
    {
        return $this->createQueryBuilder('user');
    }

    public function getLatestFollowers()
    {
        return $this->commonQuery()
            ->join('user.followers', 'f')
            ->join('f.user', 'follower')
            ->where('f.isNotificationEmail IS NULL')
            ->addSelect(array('user', 'f', 'follower'))
            ->orderBy('follower.firstName')
            ->getQuery()
            ->getResult();
    }
}
