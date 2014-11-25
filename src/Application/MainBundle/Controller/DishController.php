<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Review;
use Application\MainBundle\Form\Type\ReviewType;

class DishController extends Controller
{
    /**
     * @Route("/restaurant/{country_slug}/{locality_slug}/{restaurant_slug}/{dish_slug}", name="dish_show")
     * @Method("get|post")
     * @Template()
     */
    public function showAction(Request $request, $country_slug, $locality_slug, $restaurant_slug, $dish_slug)
    {
        $restaurant = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Restaurant')
            ->findOneBy(array('slug' => $restaurant_slug));

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant not found');
        }

        // @todo : verify $country_slug & $locality_slug

        $dish = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Dish')
            ->findOneBy(array('slug' => $dish_slug));

        if (!$dish) {
            throw $this->createNotFoundException('Dish not found');
        }

        // review form begin
        $review = new Review();
        $review->setWhen(new \DateTime("now"));

        $form_review = $this->createForm(
            new ReviewType(),
            $review,
            array('action' => $this->getDishUrl($dish))
        );

        if ($request->getMethod() === 'POST') {
            $form_review->handleRequest($request);
            if ($form_review->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $review->setUser($this->getUser());
                $review->setDish($dish);
                $dish->getReviews()->add($review);
                $em->persist($review);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'The review is created. Thank you!');

                // redirect
                return $this->redirect($this->getDishUrl($dish));
            }
        }
        // review form end

        return array(
            'dish' => $dish,
            'form_review' => $form_review->createView(),
        );
    }

    /**
     * @param $dish
     * @return string
     */
    private function getDishUrl($dish)
    {
        return $this->generateUrl('dish_show', array(
            'restaurant_slug' => $dish->getRestaurant()->getSlug(),
            'locality_slug' => $dish->getRestaurant()->getLocality()->getSlug(),
            'country_slug' => $dish->getRestaurant()->getCountry()->getSlug(),
            'dish_slug' => $dish->getSlug(),
        ));
    }
}
