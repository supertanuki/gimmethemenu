<?php

namespace Application\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\UserFollowing;

class UsersController extends Controller
{
    /**
     * List of top users
     *
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
     * Follow a user
     *
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

        // already followed ?
        $userFollowing = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:UserFollowing')
            ->findOneBy(array(
                'user' => $current_user,
                'userFollowed' => $userToFollow
            ));

        if (!$userFollowing) {
            $userFollowing = new UserFollowing();
            $userFollowing->setUser($current_user);
            $userFollowing->setUserFollowed($userToFollow);

            $em = $this->getDoctrine()->getManager();
            $em->persist($userFollowing);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', sprintf('Now you follow %s!', $userToFollow));
        }

        return $this->redirect($this->generateUrl('user_timeline', array('slug' => $userToFollow->getSlug())));
    }


    /**
     * Unfollow a user
     *
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

        // user is followed ?
        $userFollowing = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:UserFollowing')
            ->findOneBy(array(
                'user' => $current_user,
                'userFollowed' => $userToUnfollow
            ));

        if ($userFollowing) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userFollowing);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', sprintf('%s is no longer followed.', $userToUnfollow));
        }

        return $this->redirect($this->generateUrl('user_timeline', array('slug' => $userToUnfollow->getSlug())));
    }


    /**
     * Get the user followed people
     *
     * @Route("/user/{slug}/followed", name="user_followed")
     * @Method("get")
     * @Template()
     */
    public function followedAction($slug)
    {
        $user = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:User')
            ->findOneBy(array('slug' => $slug));

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return array(
            'user' => $user,
        );
    }

    /**
     * Get the user followers
     *
     * @Route("/user/{slug}/followers", name="user_followers")
     * @Method("get")
     * @Template()
     */
    public function followersAction($slug)
    {
        $user = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:User')
            ->findOneBy(array('slug' => $slug));

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return array(
            'user' => $user,
        );
    }
}
