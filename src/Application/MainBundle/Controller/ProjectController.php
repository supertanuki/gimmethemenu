<?php

namespace Application\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\MainBundle\Form\Type\ProjectResponseType;
use Application\MainBundle\Entity\Project;
use Application\MainBundle\Entity\ProjectResponse;
use Application\MainBundle\Entity\ProjectResponseFile;

class ProjectController extends Controller
{
    /**
     * @Route("/projets", name="project_index")
     * @Template()
     */
    public function indexAction()
    {
        $projects = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Project')
            ->findPublishedProjects();

        $categories = $this->getCategories();

        return array(
            'categories' => $categories,
            'projects' => $projects
        );
    }

    /**
     * @Route("/mon-profil/mes-projets", name="profile_project_index")
     * @Template()
     */
    public function myProjectsAction()
    {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $projects = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Project')
            ->findMyProjects($this->getUser());

        return array(
            'projects' => $projects,
            'published_status' => $this->getPublishedStatus()
        );
    }

    /**
     * @Route("/projets/{slug}", name="project_category")
     * @Template()
     */
    public function categoryAction($slug)
    {
        $category = $this->getCategoryBySlug($slug);

        $projects = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Project')
            ->findPublishedProjects($category);

        $categories = $this->getCategories();

        return array(
            'categories' => $categories,
            'category' => $category,
            'projects' => $projects
        );
    }

    private function getCategories()
    {
        return $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Category')
            ->findBy(array(), array('rank' => 'ASC'));
    }

    /**
     * @Route("/projets/{slug_category}/{slug_project}", name="project_show")
     * @Method("get|post")
     * @Template()
     */
    public function showAction($slug_category, $slug_project)
    {
        $category = $this->getCategoryBySlug($slug_category);

        $project = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Project')
            ->findPublishedProjectBySlug($slug_project);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $projectResponses = array();
        $form_project_response_add = null;
        $forms_project_response_delete = null;

        if ($this->getUser()) {
            // responses of the current user
            $projectResponses = $this->getDoctrine()
                ->getRepository('ApplicationMainBundle:ProjectResponse')
                ->findBy(
                    array('user' => $this->getUser(), 'project' => $project),
                    array('createdAt' => 'ASC')
                );

            foreach ($projectResponses as $projectResponse) {
                $forms_project_response_delete[$projectResponse->getId()] = $this->createDeleteForm($projectResponse->getId())->createView();
            }

            // form default response
            $projectResponse = new ProjectResponse();

            // form default file
            $projectResponseFile = new ProjectResponseFile();
            $projectResponseFile->setProjectResponse($projectResponse);
            $projectResponse->getProjectResponseFiles()->add($projectResponseFile);

            $form_project_response = $this->createForm(
                    new ProjectResponseType(),
                    $projectResponse,
                    array(
                        'action' => $this->generateUrl('project_show', array(
                            'slug_category' => $slug_category,
                            'slug_project' => $slug_project
                        ))
                    )
                )
                ->add('submit', 'submit', array('label' => 'Valider'));

            // form post
            $request = $this->getRequest();
            if ($request->getMethod() === 'POST') {
                $form_project_response->handleRequest($request);

                if ($form_project_response->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $projectResponse->setUser($this->getUser());
                    $projectResponse->setProject($project);
                    $em->persist($projectResponse);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('info', 'Proposition ajoutée');

                    return $this->redirect(
                        $this->getProjectUrl($slug_category, $slug_project)
                    );
                }
            }

            // add form
            $form_project_response_add = $form_project_response->createView();
        }

        return array(
            'category' => $category,
            'project' => $project,
            'form_project_response_add' => $form_project_response_add,
            'forms_project_response_delete' => $forms_project_response_delete,
            'project_responses' => $projectResponses
        );
    }

    /**
     * Displays a form to edit an existing ProjectResponse entity.
     *
     * @Route("/projets/reponse/{id}/edit", name="projectresponse_edit")
     * @Method("GET")
     * @Template("ApplicationMainBundle:ProjectResponse:edit.html.twig")
     */
    public function editAction($id)
    {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $projectResponse = $em->getRepository('ApplicationMainBundle:ProjectResponse')->find($id);
        if (!$projectResponse) {
            throw $this->createNotFoundException('Unable to find ProjectResponse entity.');
        }

        if ($this->getUser()->getId() != $projectResponse->getUser()->getId()) {
            throw $this->createNotFoundException('It is not your Response.');
        }

        $editForm = $this->createEditForm($projectResponse);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'project_response' => $projectResponse,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ProjectResponse entity.
     *
     * @Route("/projets/reponse/{id}/update", name="projectresponse_update")
     * @Method("PUT")
     * @Template("ApplicationMainBundle:ProjectResponse:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $projectResponse = $em->getRepository('ApplicationMainBundle:ProjectResponse')->find($id);

        if (!$projectResponse) {
            throw $this->createNotFoundException('Unable to find ProjectResponse entity.');
        }

        $project = $projectResponse->getProject();

        if ($this->getUser()->getId() != $projectResponse->getUser()->getId()) {
            throw $this->createNotFoundException('It is not your Response.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($projectResponse);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            // force the updatedAt of the projectResponse.
            // now done in ProjectResponseListener
            // $projectResponse->setUpdatedAt(new \DateTime('now'));

            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Proposition modifiée');

            return $this->redirect(
                $this->getProjectUrl(
                    $project->getCategory()->getSlug(),
                    $project->getSlug()
                )
            );
        }

        return array(
            'project_response' => $projectResponse,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ProjectResponse entity.
     *
     * @Route("/projets/reponse/{id}/delete", name="projectresponse_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $em = $this->getDoctrine()->getManager();
        $projectResponse = $em->getRepository('ApplicationMainBundle:ProjectResponse')->find($id);
        if (!$projectResponse) {
            throw $this->createNotFoundException('Unable to find Response entity.');
        }

        if ($this->getUser()->getId() != $projectResponse->getUser()->getId()) {
            throw $this->createNotFoundException('It is not your Response.');
        }

        $project = $projectResponse->getProject();

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->remove($projectResponse);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Proposition supprimée');
        }

        return $this->redirect(
            $this->getProjectUrl(
                $project->getCategory()->getSlug(),
                $project->getSlug()
            )
        );
    }

    /**
     * Creates a form to edit a ProjectResponse entity.
     *
     * @param ProjectResponse $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProjectResponse $entity)
    {
        $form = $this->createForm(new ProjectResponseType(), $entity, array(
                'action' => $this->generateUrl('projectresponse_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            ))
            ->add('submit', 'submit', array('label' => 'Modifier'));

        return $form;
    }

    /**
     * Creates a form to delete a ProjectResponse entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('projectresponse_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer cette proposition'))
            ->getForm()
            ;
    }

    /**
     * get the category.
     *
     * @param string $slug
     *
     * @return string
     */
    private function getCategoryBySlug($slug)
    {
        $category = $this->getDoctrine()
            ->getRepository('ApplicationMainBundle:Category')
            ->findOneBy(array('slug' => $slug));

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        return $category;
    }

    /**
     * Generate the project url.
     *
     * @param string $slug_category, string $slug_project
     *
     * @return string
     */
    private function getProjectUrl($slug_category, $slug_project)
    {
        return $this->generateUrl('project_show', array(
                    'slug_category' => $slug_category,
                    'slug_project' => $slug_project
                ));
    }

    /**
     * Get list of status safe "not published".
     *
     * @param string $slug_category, string $slug_project
     *
     * @return string
     */
    private function getPublishedStatus()
    {
        $publishedStatus = Project::$STATUS_LABEL;
        foreach ($publishedStatus as $id => $status) {
            if ($id == Project::STATUS_NOT_PUBLISHED) {
                unset($publishedStatus[$id]);
            }
        }

        return $publishedStatus;
    }
}
