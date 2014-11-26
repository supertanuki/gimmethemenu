<?php

namespace Application\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class DishRepository extends EntityRepository
{
    private function commonQuery()
    {
        return $this->createQueryBuilder('r');
    }
}
