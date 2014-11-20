<?php

namespace TE\Bundle\MainBundle\Controller;

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
        $projects = $this->getDoctrine()
            ->getRepository('TEMainBundle:Project')
            ->findBy(
                array('status' => \TE\Bundle\MainBundle\Entity\Project::STATUS_PUBLISHED),
                array('createdAt' => 'DESC')
            );

        return array(
            'projects' => $projects
        );
    }
}
