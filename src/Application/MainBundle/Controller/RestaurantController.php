<?php

namespace Application\MainBundle\Controller;

use Application\MainBundle\Entity\Review;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Restaurant;
use Application\MainBundle\Entity\RestaurantMenuFile;
use Application\MainBundle\Entity\Country;
use Application\MainBundle\Entity\Locality;
use Application\MainBundle\Entity\Dish;
use Application\MainBundle\Form\Type\RestaurantMenuType;
use Application\MainBundle\Form\Type\DishType;

class RestaurantController extends Controller
{
    /**
     * @Route("/restaurant/get", name="restaurant_get")
     * @Method("get")
     *
     * Paramters get :
     * place_id
     * name
     * address
     * full_address
     * locality
     * country
     * international_phone_number
     * location_lat
     * location_lng
     */
    public function getAction(Request $request)
    {
        $place_id = $request->query->get('place_id');
        $name = $request->query->get('name');
        $address = $request->query->get('address');
        $full_address = $request->query->get('full_address');
        $localityName = $request->query->get('locality');
        $countryName = $request->query->get('country');
        $international_phone_number = $request->query->get('international_phone_number');
        $location_lat = $request->query->get('location_lat');
        $location_lng = $request->query->get('location_lng');

        $restaurant = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Restaurant')
            ->findOneBy(array('ggPlaceId' => $place_id));

        // create new one
        if (!$restaurant
            && $place_id
            && $name
            && $address
            && $full_address
            && $localityName
            && $countryName
            && $location_lat
            && $location_lng
        ) {
            // get the entity manager
            $em = $this->getDoctrine()->getManager();

            // find or create the country
            $country = $this->getDoctrine()
                ->getRepository('ApplicationMainBundle:Country')
                ->findOneBy(array('name' => $countryName));

            if (!$country) {
                $country = new Country();
                $country->setName($countryName);
                $em->persist($country);
            }

            // find or create the locality
            $locality = $this->getDoctrine()
                ->getRepository('ApplicationMainBundle:Locality')
                ->findOneBy(array('name' => $localityName));

            if (!$locality) {
                $locality = new Locality();
                $locality->setName($localityName);
                $em->persist($locality);
            }

            // create the restaurant
            $restaurant = new Restaurant();
            $restaurant->setGgPlaceId($place_id);
            $restaurant->setName($name);
            $restaurant->setAddress($address);
            $restaurant->setFullAddress($full_address);
            $restaurant->setLocality($locality);
            $restaurant->setCountry($country);
            $restaurant->setLocationLat($location_lat);
            $restaurant->setLocationLng($location_lng);
            $restaurant->setInternationalPhoneNumber($international_phone_number);

            $em->persist($restaurant);
            $em->flush();
        }

        // redirect to the restaurant page
        if ($restaurant) {
            return $this->redirect($this->getRestaurantUrl($restaurant));
        }

        throw $this->createNotFoundException('Nothing to do !');
    }

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

        $dishes = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Dish')
            ->findBy(array('restaurant' => $restaurant));

        $form_menu = $this->getFormRestaurantMenu($request, $restaurant);
        if ($form_menu instanceof RedirectResponse) {
            return $form_menu;
        }

        $form_dish = $this->getFormDish($request, $restaurant);
        if ($form_dish instanceof RedirectResponse) {
            return $form_dish;
        }

        return array(
            'restaurant' => $restaurant,
            'restaurant_url' => $this->getRestaurantUrl($restaurant),
            'dishes' => $dishes,
            'form_restaurant_menu' => $form_menu->createView(),
            'form_dish' => $form_dish->createView(),
        );
    }

    /**
     * @Route("/restaurant", name="restaurant_search")
     * @Method("get")
     * @Template()
     */
    public function searchAction()
    {
        return array();
    }

    /*
     * return the route for restaurant_show
     */
    private function getRestaurantUrl($restaurant)
    {
        return $this->generateUrl('restaurant_show', array(
            'restaurant_slug' => $restaurant->getSlug(),
            'locality_slug' => $restaurant->getLocality()->getSlug(),
            'country_slug' => $restaurant->getCountry()->getSlug()
        ));
    }

    private function getFormRestaurantMenu(Request $request, $restaurant)
    {
        // form default file
        $restaurantMenuFile = new RestaurantMenuFile();
        $restaurant_tmp = new Restaurant();
        $restaurant_tmp->getRestaurantMenuFiles()->add($restaurantMenuFile);

        $form_restaurant_menu = $this->createForm(
            new RestaurantMenuType(),
            $restaurant_tmp,
            array('action' => $this->getRestaurantUrl($restaurant))
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
                return $this->redirect($this->getRestaurantUrl($restaurant));
            }
        }

        return $form_restaurant_menu;
    }

    private function getFormDish(Request $request, $restaurant)
    {
        $dish = new Dish();
        $review = new Review();
        $review->setDish($dish);
        $dish->getReviews()->add($review);

        $form_dish = $this->createForm(
            new DishType(),
            $dish,
            array('action' => $this->getRestaurantUrl($restaurant))
        );

        if ($request->getMethod() === 'POST') {
            $form_dish->handleRequest($request);
            if ($form_dish->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $dish->setUser($this->getUser());
                $dish->setRestaurant($restaurant);
                $review->setUser($this->getUser());
                $em->persist($dish);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'The dish is created. Thank you!');

                // redirect
                return $this->redirect($this->getRestaurantUrl($restaurant));
            }
        }

        return $form_dish;
    }
}
