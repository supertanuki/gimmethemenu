<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UsersController extends Controller
{
    /**
     * @Route("/users/top", name="users_top")
     * @Template()
     */
    public function topAction()
    {
        $users = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:User')
            ->findAll();

        return array(
            'users' => $users,
        );
    }
}
