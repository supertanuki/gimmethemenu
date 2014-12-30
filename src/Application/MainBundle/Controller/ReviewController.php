<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Entity\Review;
use Application\MainBundle\Form\Type\ReviewType;


class ReviewController extends Controller
{
    /**
     * Displays a form to edit an existing Review entity.
     *
     * @Route("/review/{id}/edit", name="review_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $review = $em->getRepository('ApplicationMainBundle:Review')->find($id);

        if (!$review) {
            throw $this->createNotFoundException('Unable to find this review.');
        }

        if ($review->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('You are not allowed to edit this review');
        }

        $editForm = $this->createEditForm($review);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'review'      => $review,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Review entity.
    *
    * @param Review $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Review $review)
    {
        $form = $this->createForm(new ReviewType(), $review, array(
            'action' => $this->generateUrl('review_update', array('id' => $review->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Review entity.
     *
     * @Route("/review/{id}/update", name="review_update")
     * @Method("PUT")
     * @Template("ApplicationMainBundle:Review:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $review = $em->getRepository('ApplicationMainBundle:Review')->find($id);

        if (!$review) {
            throw $this->createNotFoundException('Unable to find this review.');
        }

        if ($review->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('You are not allowed to edit this review');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($review);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Your review is updated!');
            return $this->redirect($this->generateUrl('dish_show', $review->getDish()->getParamsForUrl()));
        }

        return array(
            'review'      => $review,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Review entity.
     *
     * @Route("/review/{id}/delete", name="review_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $review = $em->getRepository('ApplicationMainBundle:Review')->find($id);

        if (!$review) {
            throw $this->createNotFoundException('Unable to find Review entity.');
        }

        if ($review->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('You are not allowed to delete this review');
        }

        $dish = $review->getDish();

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->remove($review);
            $em->flush();
            $this->get('session')->getFlashBag()->add('info', 'Your review is deleted.');
        }

        return $this->redirect($this->generateUrl('dish_show', $dish->getParamsForUrl()));
    }

    /**
     * Creates a form to delete a Review entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('review_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete this review', 'attr' => array('class' => 'btn btn-danger')))
            ->getForm()
        ;
    }
}
