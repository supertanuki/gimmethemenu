<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{
    /**
     * Route "fos_user_security_login" defined in app/config/routing.yml
     * @Template()
     */
    public function loginAction()
    {
        return array();
    }
}
