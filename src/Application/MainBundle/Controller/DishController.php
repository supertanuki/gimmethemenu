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
use Application\MainBundle\Form\Type\DishChildrenReviewType;
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
     * @Route("/restaurant/{country_slug}/{locality_slug}/{restaurant_slug}/meal/add", name="add_group_dishes")
     * @Method("get|post")
     */
    public function addMealAction(Request $request, $country_slug, $locality_slug, $restaurant_slug)
    {
        if (!$this->getUser()) {
            throw $this->createNotFoundException('You are not logged buddy!');
        }

        $restaurant = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Restaurant')
            ->findOneBy(array('slug' => $restaurant_slug));

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant not found');
        }

        $dish = new Dish();
        $dish->setRestaurant($restaurant);
        $dish->setUser($this->getUser());

        $childrenDish1 = new Dish();
        $childrenDish1->setParent($dish);
        $childrenDish1->setRestaurant($restaurant);
        $childrenDish1->setUser($this->getUser());
        $dish->getDishes()->add($childrenDish1);

        $review1 = new Review();
        $review1->setDish($childrenDish1);
        $review1->setUser($this->getUser());
        $childrenDish1->getReviews()->add($review1);

        $form_dish = $this->createForm(
            new DishGroupType(),
            $dish
        );

        $form_dish->get('when')->setData(new \DateTime("now"));

        if ($request->getMethod() === 'POST') {
            $form_dish->handleRequest($request);
            if ($form_dish->isValid()) {

                $review1->setWhen($form_dish->get('when')->getData());

                $em = $this->getDoctrine()->getManager();
                $em->persist($dish);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Thank you! The meal is created. You can now add another dish');

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
     * @Route("/restaurant/{country_slug}/{locality_slug}/{restaurant_slug}/{dish_slug}/dish/add", name="dish_child_add")
     * @Method("get|post")
     */
    public function addDishToMealAction(Request $request, $country_slug, $locality_slug, $restaurant_slug, $dish_slug)
    {
        if (!$this->getUser()) {
            throw $this->createNotFoundException('You are not logged buddy!');
        }

        $restaurant = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Restaurant')
            ->findOneBy(array('slug' => $restaurant_slug));

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant not found');
        }

        $dish = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Dish')
            ->findOneBy(array('slug' => $dish_slug));

        $otherChildDish = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Dish')
            ->findOneBy(array(
                'parent' => $dish,
                'user' => $this->getUser()
            ), array(
                'createdAt' => 'DESC'
            ));

        if ($otherChildDish) {
            $otherChildDishReview = $this->getDoctrine()
                ->getRepository('ApplicationMainBundle:Review')
                ->findOneBy(array(
                    'dish' => $otherChildDish,
                    'user' => $this->getUser()
                ), array(
                    'createdAt' => 'DESC'
                ));
        }

        if (!$dish) {
            throw $this->createNotFoundException('Dish not found');
        }

        $childDish = new Dish();
        $childDish->setParent($dish);
        $childDish->setRestaurant($restaurant);
        $childDish->setUser($this->getUser());
        $dish->getDishes()->add($childDish);

        $review = new Review();
        $review->setDish($childDish);
        $review->setUser($this->getUser());
        $childDish->getReviews()->add($review);

        if ($otherChildDish && $otherChildDishReview) {
            $review->setWhen($otherChildDishReview->getWhen());
        } else {
            $review->setWhen(new \DateTime("now"));
        }

        $form_dish = $this->createForm(
            new DishChildrenReviewType(),
            $childDish
        );

        if ($request->getMethod() === 'POST') {
            $form_dish->handleRequest($request);
            if ($form_dish->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($dish);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'The dish is created. Thank you!');

                // redirect
                return $this->redirect($this->getDishUrl($dish));
            }
        }

        return $this->render(
            'ApplicationMainBundle:Dish:add.html.twig',
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
        return $this->generateUrl('dish_show', $dish->getParamsForUrl());
    }
}
