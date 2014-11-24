<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{
    /**
     * Route "fos_user_security_login" defined in app/config/routing.yml
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($this->getUser()) {
            $redirect = $request->query->get('redirect');

            if (!$redirect) {
                $redirect = $this->generateUrl('restaurant_search');
            }

            return $this->redirect($redirect);
        }

        return array();
    }

    /**
     * @Route("/login-end", name="login_end")
     */
    public function loginEndAction()
    {
        $this->get('session')->getFlashBag()->add('info', 'Happy to see you again ! Do you want add a review ?');

        // redirect
        return $this->redirect($this->generateUrl('restaurant_search'));
    }
}
