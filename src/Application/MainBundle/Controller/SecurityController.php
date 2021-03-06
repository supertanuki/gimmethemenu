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
     * Login Controller for "fos_user_security_login" defined in app/config/routing.yml
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($this->getUser()) {
            $this->setWelcomeMessage();

            $redirect = $request->query->get('redirect');

            $last_visited_restaurant_url = $this->get('session')->get('last_visited_restaurant_url');
            if (!$redirect && $last_visited_restaurant_url) {
                $redirect = $last_visited_restaurant_url;
            }

            if (!$redirect) {
                $redirect = $this->generateUrl('restaurant_search');
            }

            return $this->redirect($redirect);
        }

        return array();
    }

    /**
     * Landing page after login
     *
     * @Route("/login-end", name="login_end")
     */
    public function loginEndAction()
    {
        $this->setWelcomeMessage();

        // redirect
        return $this->redirect($this->generateUrl('restaurant_search'));
    }

    /**
     * Set a welcome message in flashBag
     */
    private function setWelcomeMessage()
    {
        $this->get('session')->getFlashBag()->add('info', sprintf('Hi %s, happy to see you!', $this->getUser()->getFirstName()));
    }
}
