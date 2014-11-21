<?php

namespace Application\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class RestaurantRepository extends EntityRepository
{
    private function commonQuery()
    {
        return $this->createQueryBuilder('r');
    }
}
