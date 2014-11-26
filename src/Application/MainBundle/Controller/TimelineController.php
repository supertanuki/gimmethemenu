<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TimelineController extends Controller
{
    /**
     * @Route("/my-timeline", name="my_timeline")
     * @Method("get")
     * @Template()
     */
    public function showAction()
    {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        return array(
            'register_date' => $this->getUser()->getCreatedAt(),
        );
    }
}
