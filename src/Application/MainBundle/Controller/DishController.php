<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Dish;

class DishController extends Controller
{
    /**
     * @Route("/restaurant/{country_slug}/{locality_slug}/{restaurant_slug}", name="restaurant_show")
     * @Method("get|post")
     * @Template()
     */
    public function showAction(Request $request, $country_slug, $locality_slug, $restaurant_slug)
    {
        $restaurant = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Restaurant')
            ->findOneBy(array('slug' => $restaurant_slug));

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant not found');
        }

        // @todo : verify $country_slug & $locality_slug

        // form default file
        $restaurantMenuFile = new RestaurantMenuFile();
        $restaurant_tmp = new Restaurant();
        $restaurant_tmp->getRestaurantMenuFiles()->add($restaurantMenuFile);

        $form_restaurant_menu = $this->createForm(
            new RestaurantMenuType(),
            $restaurant_tmp,
            array('action' => $this->generateUrl('restaurant_show', array(
                    'restaurant_slug' => $restaurant_slug,
                    'locality_slug' => $locality_slug,
                    'country_slug' => $country_slug
                ))
            )
        );


        if ($request->getMethod() === 'POST') {
            $form_restaurant_menu->handleRequest($request);
            if ($form_restaurant_menu->isValid()) {
                $em = $this->getDoctrine()->getManager();
                foreach ($restaurant_tmp->getRestaurantMenuFiles() as $menuFile) {
                    $menuFile->setUser($this->getUser());
                    $menuFile->setRestaurant($restaurant);
                    $restaurant->getRestaurantMenuFiles()->add($menuFile);
                }
                $em->persist($restaurant);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Your photo is online. Thank you!');

                // redirect
                return $this->redirect(
                    $this->generateUrl('restaurant_show', array(
                        'restaurant_slug' => $restaurant_slug,
                        'locality_slug' => $locality_slug,
                        'country_slug' => $country_slug
                    ))
                );
            }
        }

        return array(
            'restaurant' => $restaurant,
            'form_restaurant_menu' => $form_restaurant_menu->createView(),
        );
    }
}
