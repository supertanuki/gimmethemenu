<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Dish;
use Application\MainBundle\Entity\Review;
use Application\MainBundle\Form\Type\ReviewType;
use Application\MainBundle\Form\Type\DishGroupType;

class DishController extends Controller
{
    /**
     * @Route("/restaurant/{country_slug}/{locality_slug}/{restaurant_slug}/{dish_slug}", name="dish_show")
     * @Method("get|post")
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

            // onError
            return $this->render(
                'ApplicationMainBundle:Review:add.html.twig',
                array(
                    'dish' => $dish,
                    'dish_url' => $this->getDishUrl($dish),
                    'form_review' => $form_review->createView(),
                )
            );
        }
        // review form end

        return $this->render(
            'ApplicationMainBundle:Dish:show.html.twig',
            array(
                'dish' => $dish,
                'form_review' => $form_review->createView(),
            )
        );
    }

    /**
     * @Route("/restaurant/{country_slug}/{locality_slug}/{restaurant_slug}/dish/add-group", name="add_group_dishes")
     * @Method("get|post")
     */
    public function addGroupAction(Request $request, $country_slug, $locality_slug, $restaurant_slug)
    {
        $restaurant = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Restaurant')
            ->findOneBy(array('slug' => $restaurant_slug));

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant not found');
        }

        if (!$this->getUser()) {
            throw $this->createNotFoundException('You are not logged buddy!');
        }

        $dish = new Dish();

        $childrenDish1 = new Dish();
        $childrenDish1->setParent($dish);
        $childrenDish1->setRestaurant($restaurant);
        $dish->getDishes()->add($childrenDish1);
        $dish->setUser($this->getUser());
        $review1 = new Review();
        $review1->setDish($childrenDish1);
        $review1->setUser($this->getUser());
        $childrenDish1->getReviews()->add($review1);

        $childrenDish2 = new Dish();
        $childrenDish2->setParent($dish);
        $childrenDish2->setRestaurant($restaurant);
        $dish->getDishes()->add($childrenDish2);
        $review2 = new Review();
        $review2->setDish($childrenDish2);
        $review2->setUser($this->getUser());
        $childrenDish2->getReviews()->add($review2);


        $form_dish = $this->createForm(
            new DishGroupType(),
            $dish,
            array('action' => $request->getBaseUrl())
        );

        $form_dish->get('when')->setData(new \DateTime("now"));

        if ($request->getMethod() === 'POST') {
            $form_dish->handleRequest($request);
            if ($form_dish->isValid()) {

                $review1->setWhen($form_dish->get('when')->getData());
                $review2->setWhen($form_dish->get('when')->getData());

                $em = $this->getDoctrine()->getManager();
                $em->persist($dish);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'The dish is created. Thank you!');

                // redirect
                return $this->redirect($this->getDishUrl($dish));
            }
        }

        return $this->render(
            'ApplicationMainBundle:Dish:addGroup.html.twig',
            array(
                'restaurant' => $restaurant,
                'form_dish' => $form_dish->createView(),
            )
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
