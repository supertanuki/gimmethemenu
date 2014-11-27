<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TimelineController extends Controller
{
    /**
     * @Route("/user/{slug}/timeline", name="user_timeline")
     * @Method("get")
     * @Template()
     */
    public function showAction($slug)
    {
        $user = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:User')
            ->findOneBy(array('slug' => $slug));

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

//        $logs = $this->getDoctrine()
//            ->getRepository('ApplicationMainBundle:User')
//            ->getLogs($user);

        return array(
            'user' => $user,
        );
    }
}
