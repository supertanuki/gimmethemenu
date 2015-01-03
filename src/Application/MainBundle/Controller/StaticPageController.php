<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;

class StaticPageController extends Controller
{
    /**
     * @Route("/about", name="about")
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }

    /**
     * @Route("/changelog", name="changelog")
     * @Template()
     */
    public function changelogAction()
    {
        return array();
    }

    /**
     * @Route("/send-email", name="send-email")
     */
    public function emailAction()
    {
        $dispatcher = $this->get('hip_mandrill.dispatcher');

        $message = new Message();

        $message
            ->addTo('supertanuki@gmail.com')
            ->setSubject('Test Mandrill')
            ->setHtml('<html><body><h1>Tu vas bien ?</h1></body></html>')
            ;

        $result = $dispatcher->send($message);

        return new Response('<h1>Email</h1><pre>' . print_r($result, true) . '</pre>');
    }
}
