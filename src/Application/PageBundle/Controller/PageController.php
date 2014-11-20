<?php

namespace Application\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends Controller
{
    /**
     * @Route("/{slug}", name="page")
     * @Template()
     */
    public function indexAction($slug)
    {
        $page = $this->getDoctrine()
            ->getRepository('ApplicationPageBundle:Page')
            ->findOneBy(array('slug' => $slug));

        if (!$page) {
            throw $this->createNotFoundException('Page not found');
        }

        return array('page' => $page);
    }

    /**
     * @Template()
     */
    public function listAction()
    {
        $pages = $this->getDoctrine()
            ->getRepository('ApplicationPageBundle:Page')
            ->findBy(array('isEnabled' => true));

        return array('pages' => $pages);
    }
}
