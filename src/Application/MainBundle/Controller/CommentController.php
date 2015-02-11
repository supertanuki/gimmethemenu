<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Comment;

/**
 * Comment controller.
 */
class CommentController extends Controller
{

    /**
     * Add a comment
     *
     * @Route("/comment/{review_id}/create", name="comment_create")
     * @Method("POST")
     */
    public function createAction(Request $request, $review_id)
    {
        if (!$this->getUser()) {
            throw $this->createNotFoundException('User not logged');
        }

        $this->checkCsrf($request, 'comment');

        $em = $this->getDoctrine()->getManager();
        $review = $em->getRepository('ApplicationMainBundle:Review')->find($review_id);
        if (!$review) {
            throw $this->createNotFoundException('Unable to find review.');
        }

        $comment_input = $request->request->get('comment');
        if (!trim(strlen($comment_input))) {
            throw $this->createAccessDeniedException('No Comment');
        }
        if ($comment_input) {
            $comment = new Comment();
            $comment->setUser($this->getUser());
            $comment->setReview($review);
            $comment->setComment($comment_input);
            $em->persist($comment);
            $em->flush();
        }

        $response = new JsonResponse();
        $response->setData(array(
            'id' => $comment->getId()
        ));
        return $response;
    }

    /**
     * Delete a comment
     *
     * @Route("/comment/{id}/delete", name="comment_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        if (!$this->getUser()) {
            throw $this->createNotFoundException('User not logged');
        }

        $this->checkCsrf($request, 'comment');

        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('ApplicationMainBundle:Comment')->find($id);
        if (!$comment) {
            throw $this->createNotFoundException('Unable to find comment.');
        }

        if ($comment->getUser() != $this->getUser()) {
            throw $this->createAccessDeniedException('It is not your comment');
        }

        $em->remove($comment);
        $em->flush();

        $response = new JsonResponse();
        return $response;
    }

    /**
     * Check comment CSRF
     *
     * @param Request $request
     * @param $name
     * @param string $query
     * @return bool
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function checkCsrf(Request $request, $name, $query = '_token')
    {
        $csrfProvider = $this->get('form.csrf_provider');

        if (!$csrfProvider->isCsrfTokenValid($name, $request->query->get($query))) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('CSRF token is invalid.');
        }

        return true;
    }
}
