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
//        $projects = $this->getDoctrine()
//            ->getRepository('ApplicationMainBundle:Project')
//            ->findBy(
//                array('status' => \Application\MainBundle\Entity\Project::STATUS_PUBLISHED),
//                array('createdAt' => 'DESC')
//            );

        return array(
//            'projects' => $projects
        );
    }
}
