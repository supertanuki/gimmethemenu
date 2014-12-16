<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TimelineController extends Controller
{
    /**
     * @Route("/user/{slug}/timeline", name="user_timeline")
     * @Method("get")
     * @Template()
     */
    public function showAction($slug)
    {
        $user = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:User')
            ->findOneBy(array('slug' => $slug));

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $logs = array();

        //added dishes
        $dishes = array();
//        $dishes = $this->getDoctrine()
//            ->getRepository('ApplicationMainBundle:Dish')
//            ->findBy(
//                array('user' => $user),
//                array('createdAt' => 'DESC')
//            );

        //added menuFile
        $menuFiles = array();
//        $menuFiles = $this->getDoctrine()
//            ->getRepository('ApplicationMainBundle:RestaurantMenuFile')
//            ->findBy(
//                array('user' => $user),
//                array('createdAt' => 'DESC')
//            );

        //added reviews
        $reviews = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Review')
            ->findBy(
                array('user' => $user),
                array(
                    'when' => 'DESC',
                    'createdAt' => 'DESC'
                )
            );

        $dateFormat = 'Y-m-d H:i:s';

        foreach ($dishes as $dish) {
            $logs[$dish->getCreatedAt()->format($dateFormat)]['dish'] = $dish;
        }

        foreach ($menuFiles as $menuFile) {
            $logs[$menuFile->getCreatedAt()->format($dateFormat)]['menuFile'] = $menuFile;
        }

        foreach ($reviews as $review) {
            $logs[$review->getWhen()->format($dateFormat)]['reviews'][] = $review;
        }

        // register date
        $logs[$user->getCreatedAt()->format($dateFormat)]['registerAt'] = $user->getCreatedAt();

        krsort ($logs);

        return array(
            'user' => $user,
            'logs' => $logs,
        );
    }
}
