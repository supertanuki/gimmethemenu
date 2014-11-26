<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $lastReviews = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Review')
            ->findBy(
                array(),
                array('createdAt' => 'DESC'),
                6
            );

        return array(
            'lastReviews' => $lastReviews
        );
    }
}
