<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Restaurant;

class RestaurantController extends Controller
{
    /**
     * @Route("/restaurant/get", name="restaurant_get")
     * @Method("get")
     * @Template()
     *
     * place_id
     * name
     * address
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
        $locality = $request->query->get('locality');
        $country = $request->query->get('country');
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
            && $locality
            && $country
            && $location_lat
            && $location_lng
        ) {
            $restaurant = new Restaurant();
            $restaurant->setGgPlaceId($place_id);
            $restaurant->setName($name);
            $restaurant->setAddress($address);
            $restaurant->setLocality($locality);
            $restaurant->setCountry($country);
            $restaurant->setLocationLat($location_lat);
            $restaurant->setLocationLng($location_lng);
            $restaurant->setInternationalPhoneNumber($international_phone_number);

            $em = $this->getDoctrine()->getManager();
            $em->persist($restaurant);
            $em->flush();
        }

        // redirect to the restaurant page
        if ($restaurant) {
            return $this->redirect($this->generateUrl('restaurant_show', array(
                'country_slug'      => $restaurant->getCountrySlug(),
                'locality_slug'     => $restaurant->getLocalitySlug(),
                'restaurant_slug'   => $restaurant->getSlug()
            )));
        }

        throw $this->createNotFoundException('Nothing to do !');
    }

    /**
     * @Route("/restaurant/{country_slug}/{locality_slug}/{restaurant_slug}", name="restaurant_show")
     * @Method("get")
     * @Template()
     */
    public function showAction($country_slug, $locality_slug, $restaurant_slug)
    {
        $restaurant = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Restaurant')
            ->findOneBy(array('slug' => $restaurant_slug));

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant not found');
        }

        return array('restaurant' => $restaurant);
    }
}
