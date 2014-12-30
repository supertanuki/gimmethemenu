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
use Application\MainBundle\Form\Type\ReviewQuickType;
use Application\MainBundle\Form\Type\DishType;
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

                $this->get('session')->getFlashBag()->add('info', 'The review is created. Thank you! __shareit__');

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
                'already_reviewed' => $this->reviewDishByUserExists($dish, $this->getUser()),
            )
        );
    }


    /**
     * @Route("/restaurant/{country_slug}/{locality_slug}/{restaurant_slug}/{dish_slug}/add-quick-review", name="review_quick_add")
     * @Method("get|post")
     */
    public function addQuickReviewAction(Request $request, $country_slug, $locality_slug, $restaurant_slug, $dish_slug)
    {
        if (!$this->getUser()) {
            throw $this->createNotFoundException('You are not logged');
        }

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

        // Check if there is already a review for this dish by this user
        if (!$this->reviewDishByUserExists($dish, $this->getUser())) {
            $this->get('session')->getFlashBag()->add('info', 'You need to have already a review to add a quick review');
            throw $this->createAccessDeniedException('You need to have already a review to add a quick review');
        }

        // review form begin
        $review = new Review();
        $review->setWhen(new \DateTime("now"));

        $form_review = $this->createForm(
            new ReviewQuickType(),
            $review
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

                $this->get('session')->getFlashBag()->add('info', 'The quick review is created. Thank you!');

                // redirect
                return $this->redirect($this->getDishUrl($dish));
            }
        }
        // review form end

        return $this->render(
            'ApplicationMainBundle:Review:addQuickReview.html.twig',
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

        $meal = new Dish();
        $meal->setRestaurant($restaurant);
        $meal->setUser($this->getUser());

        $childrenDish = new Dish();
        $childrenDish->setParent($meal);
        $childrenDish->setRestaurant($restaurant);
        $childrenDish->setUser($this->getUser());
        $meal->getDishes()->add($childrenDish);

        $review = new Review();
        $review->setDish($childrenDish);
        $review->setUser($this->getUser());
        $review->setWhen(new \DateTime("now"));
        $childrenDish->getReviews()->add($review);

        $form_dish = $this->createForm(
            new DishGroupType(),
            $meal
        );

        $dish_already_exists = null;

        if ($request->getMethod() === 'POST') {
            $form_dish->handleRequest($request);
            if ($form_dish->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($meal);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Thank you! The meal is created. You can now add another dish');

                // redirect
                return $this->redirect($this->getDishUrl($meal));
            }

            $dish_already_exists = $this->getDoctrine()
                ->getRepository('ApplicationMainBundle:Dish')
                ->findOneBy(array(
                    'restaurant' => $restaurant,
                    'name' => $meal->getName(),
                ));

            if (!$dish_already_exists) {
                $dish_already_exists = $this->getDoctrine()
                    ->getRepository('ApplicationMainBundle:Dish')
                    ->findOneBy(array(
                        'restaurant' => $restaurant,
                        'name' => $childrenDish->getName(),
                    ));
            }
        }

        return $this->render(
            'ApplicationMainBundle:Dish:addGroup.html.twig',
            array(
                'restaurant' => $restaurant,
                'dish_already_exists' => $dish_already_exists,
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

        $dish_already_exists = null;

        if ($request->getMethod() === 'POST') {
            $form_dish->handleRequest($request);
            if ($form_dish->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($dish);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'The dish and your review is created. Thank you! __shareit__');

                // redirect
                return $this->redirect($this->getDishUrl($dish));
            }

            $dish_already_exists = $this->getDoctrine()
                ->getRepository('ApplicationMainBundle:Dish')
                ->findOneBy(array(
                    'restaurant' => $restaurant,
                    'name' => $childDish->getName(),
                ));

        }

        return $this->render(
            'ApplicationMainBundle:Dish:add.html.twig',
            array(
                'restaurant' => $restaurant,
                'dish_already_exists' => $dish_already_exists,
                'form_dish' => $form_dish->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing dish.
     * @Route("/dish/{id}/edit", name="dish_edit")
     * @Method("GET")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $dish = $em->getRepository('ApplicationMainBundle:Dish')->find($id);

        if (!$dish) {
            throw $this->createNotFoundException('Unable to find this dish.');
        }

        if ($dish->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('You are not allowed to edit this dish');
        }

        $countOthersReviews = $em->getRepository('ApplicationMainBundle:Review')->countReviews($dish, $this->getUser());
        if ($countOthersReviews) {
            return $this->render(
                'ApplicationMainBundle:Dish:editNotAllowed.html.twig',
                array(
                    'dish'        => $dish,
                    'restaurant'  => $dish->getRestaurant(),
                )
            );
        }

        $editForm = $this->createEditForm($dish);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'ApplicationMainBundle:Dish:edit.html.twig',
            array(
                'dish'        => $dish,
                'restaurant'  => $dish->getRestaurant(),
                'dish_already_exists' => null,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a dish.
     * @param Dish $dish
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Dish $dish)
    {
        $form = $this->createForm(new DishType(), $dish, array(
            'action' => $this->generateUrl('dish_update', array('id' => $dish->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing dish.
     * @Route("/dish/{id}/update", name="dish_update")
     * @Method("PUT")
     * @Template("ApplicationMainBundle:Dish:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $dish = $em->getRepository('ApplicationMainBundle:Dish')->find($id);

        if (!$dish) {
            throw $this->createNotFoundException('Unable to find this dish.');
        }

        if ($dish->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('You are not allowed to edit this dish');
        }

        $countOthersReviews = $em->getRepository('ApplicationMainBundle:Review')->countReviews($dish, $this->getUser());
        if ($countOthersReviews) {
            throw $this->createNotFoundException('You are not allowed to edit this dish');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($dish);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'The dish is updated!');
            return $this->redirect($this->generateUrl('dish_show', $dish->getParamsForUrl()));
        }

        $dish_already_exists = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Dish')
            ->findOneBy(array(
                'restaurant' => $dish->getRestaurant(),
                'name' => $dish->getName(),
            ));

        return array(
            'dish'        => $dish,
            'restaurant'  => $dish->getRestaurant(),
            'dish_already_exists' => $dish_already_exists,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a dish
     * @Route("/dish/{id}/delete", name="dish_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $dish = $em->getRepository('ApplicationMainBundle:Dish')->find($id);

        if (!$dish) {
            throw $this->createNotFoundException('Unable to find dish');
        }

        if ($dish->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('You are not allowed to delete this dish');
        }

        $countOthersReviews = $em->getRepository('ApplicationMainBundle:Review')->countReviews($dish, $this->getUser());
        if ($countOthersReviews) {
            throw $this->createNotFoundException('You are not allowed to edit this dish');
        }

        $restaurant = $dish->getRestaurant();

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->remove($dish);
            $em->flush();
            $this->get('session')->getFlashBag()->add('info', 'The dish is deleted.');
        }

        return $this->redirect($this->generateUrl('restaurant_show', $restaurant->getParamsForUrl()));
    }

    /**
     * Creates a form to delete a dish.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dish_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete this dish', 'attr' => array('class' => 'btn btn-danger')))
            ->getForm()
            ;
    }

    /**
     * @param $dish
     * @return string
     */
    private function getDishUrl($dish)
    {
        return $this->generateUrl('dish_show', $dish->getParamsForUrl());
    }

    /*
     * Check if there is already a review for this dish by this user
     * @return boolean
     */
    private function reviewDishByUserExists($dish, $user)
    {
        if (!$user) {
            return false;
        }

        $review = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:review')
            ->findOneBy(array(
                'dish' => $dish,
                'user' => $user,
            ));

        if ($review) {
            return true;
        }

        return false;
    }
}
