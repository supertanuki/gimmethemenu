<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UsersController extends Controller
{
    /**
     * @Route("/users/top", name="users_top")
     * @Template()
     */
    public function topAction()
    {
        $users = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:User')
            ->findAll();

        return array(
            'users' => $users,
        );
    }

    /**
     * @Route("/user/{slug}/follow", name="follow_user")
     */
    public function followUserAction($slug)
    {
        $current_user = $this->getUser();
        if (!$current_user) {
            throw $this->createNotFoundException('User not logged');
        }

        $userToFollow = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:User')
            ->findOneBy(array('slug' => $slug));

        if (!$userToFollow) {
            throw $this->createNotFoundException('User not found');
        }

        $current_user->getFollowings()->add($userToFollow);
        $userToFollow->getFollowers()->add($current_user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($current_user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', sprintf('Now you follow %s!', $userToFollow));

        return $this->redirect($this->generateUrl('user_timeline', array('slug' => $userToFollow->getSlug())));
    }


    /**
     * @Route("/user/{slug}/unfollow", name="unfollow_user")
     */
    public function unfollowUserAction($slug)
    {
        $current_user = $this->getUser();
        if (!$current_user) {
            throw $this->createNotFoundException('User not logged');
        }

        $userToUnfollow = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:User')
            ->findOneBy(array('slug' => $slug));

        if (!$userToUnfollow) {
            throw $this->createNotFoundException('User not found');
        }

        $current_user->removeFollowing($userToUnfollow);
        $userToUnfollow->removeFollower($current_user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($current_user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', sprintf('%s is no longer followed.', $userToUnfollow));

        return $this->redirect($this->generateUrl('user_timeline', array('slug' => $userToUnfollow->getSlug())));
    }
}
