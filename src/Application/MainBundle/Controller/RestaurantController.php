<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Restaurant;
use Application\MainBundle\Entity\RestaurantMenuFile;
use Application\MainBundle\Entity\Country;
use Application\MainBundle\Entity\Locality;
use Application\MainBundle\Form\Type\RestaurantMenuType;
use Application\MainBundle\Form\Type\RestaurantMenuFileType;

class RestaurantController extends Controller
{
    /**
     * @Route("/restaurant/get", name="restaurant_get")
     * @Method("get")
     * @Template()
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
            return $this->redirect($this->generateUrl('restaurant_show', array(
                'country_slug'      => $restaurant->getCountry()->getSlug(),
                'locality_slug'     => $restaurant->getLocality()->getSlug(),
                'restaurant_slug'   => $restaurant->getSlug()
            )));
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

                $this->get('session')->getFlashBag()->add('info', 'Menu ajouté !');

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

    /**
     * @Route("/restaurant", name="restaurant_search")
     * @Method("get")
     * @Template()
     */
    public function searchAction()
    {
        return array();
    }
}
