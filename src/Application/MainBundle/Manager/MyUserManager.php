<?php

namespace Application\MainBundle\Manager;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;

class MyUserManager extends BaseUserManager
{
    /**
     * Find user by given username or email
     *
     * @param string $username
     * @param string $email
     * @return mixed
     */
//    public function findUserByUsernameAndEmail($username, $email)
//    {
//        $user = $this->repository->createQueryBuilder('u')
//            ->where('u.username = :username')
//            ->orWhere('u.email = :email')
//            ->setParameter('username', $username)
//            ->setParameter('email', $email)
//            ->getQuery()
//            ->getOneOrNullResult()
//            ;
//
//        return $user;
//    }
}