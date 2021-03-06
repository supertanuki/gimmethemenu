<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TimelineController extends Controller
{
    /**
     * Show the user timeline
     *
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

        foreach ($reviews as $review) {
            $logs[$review->getWhen()->format($dateFormat)]['reviews'][] = $review;
        }

        // register date
        $logs[$user->getCreatedAt()->format($dateFormat)]['registerAt'] = $user->getCreatedAt();

        // sort by date desc
        krsort ($logs);

        return array(
            'user' => $user,
            'logs' => $logs,
        );
    }


    /**
     * Show the activities timeline
     *
     * @Route("/activities", name="activities")
     * @Method("get")
     * @Template()
     */
    public function activitiesAction()
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('User not logged');
        }

        $logs = array();

        $reviews = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Review')
            ->getReviewsFromFollowed($user);

        $dateFormat = 'Y-m-d H:i:s';
        foreach ($reviews as $review) {
            $logs[$review->getWhen()->format($dateFormat)]['reviews'][] = $review;
        }

        // sort by date desc
        krsort ($logs);

        return array(
            'logs' => $logs,
        );
    }
}
