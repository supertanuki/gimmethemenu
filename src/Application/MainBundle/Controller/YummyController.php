<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Yummy;

/**
 * Yummy controller.
 */
class YummyController extends Controller
{
    /**
     * Add or remove a yummy
     *
     * @Route("/yummy/{review_id}", name="yummy")
     * @Method("POST")
     */
    public function addOrRemoveYummyAction(Request $request, $review_id)
    {
        if (!$this->getUser()) {
            throw $this->createNotFoundException('User not logged');
        }

        $this->checkCsrf($request, 'yummy');

        $em = $this->getDoctrine()->getManager();
        $review = $em->getRepository('ApplicationMainBundle:Review')->find($review_id);
        if (!$review) {
            throw $this->createNotFoundException('Unable to find Review.');
        }

        $yummy = $em->getRepository('ApplicationMainBundle:Yummy')->findOneBy(array(
            'review' => $review,
            'user' => $this->getUser(),
        ));

        $em = $this->getDoctrine()->getManager();

        if (!$yummy) {
            // add a yummy
            $yummy = new Yummy();
            $yummy->setUser($this->getUser());
            $yummy->setReview($review);
            $em->persist($yummy);
        } else {
            // remove a yummy
            $em->remove($yummy);
        }

        $em->flush();

        $response = new JsonResponse();
//        $response->setData(array(
//            'data' => 123
//        ));
        return $response;
    }

    protected function checkCsrf(Request $request, $name, $query = '_token')
    {
        $csrfProvider = $this->get('form.csrf_provider');

        if (!$csrfProvider->isCsrfTokenValid($name, $request->query->get($query))) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('CSRF token is invalid.');
        }

        return true;
    }
}
