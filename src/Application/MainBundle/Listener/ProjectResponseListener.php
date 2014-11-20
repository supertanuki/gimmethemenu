<?php
namespace Application\MainBundle\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Application\MainBundle\Entity\ProjectResponse;
use Application\MainBundle\Entity\Project;
use Symfony\Component\HttpFoundation\Session\Session;

class ProjectResponseListener
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /*
     * When a projectResponse is selected :
     * - Check if there is another selected projectResponse and de-selected it
     * - change the state of the project
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $em = $eventArgs->getEntityManager();
        $uow  = $em->getUnitOfWork();

        if (!($entity instanceof ProjectResponse)) {
            return;
        }

        // check if is_selected is selected
        if (!($eventArgs->hasChangedField('isSelected') && $eventArgs->getNewValue('isSelected') == true)) {
            return;
        }

        $project =  $entity->getProject();

        // project not closed ? so close it
        if ($project->getStatus() != Project::STATUS_CLOSED
            && $project->getStatus() != Project::STATUS_NOT_PUBLISHED) {

            // change the project status to closed
            $uow->scheduleExtraUpdate($project, array(
                    'status' => array($project->getStatus(), Project::STATUS_CLOSED)
                ));

            // push a message
            $this->session->getFlashBag()->add(
                'sonata_flash_success',
                sprintf('Le statut du projet "%s" a été passé à "Terminé" !', $project->getTitle())
            );
        }

        // Check if there is another selected projectResponse and de-selected it
        $projectResponsesSelected = $em->getRepository('ApplicationMainBundle:ProjectResponse')->findBy(array(
               'isSelected' => true,
               'project' => $project
            ));

        if (count($projectResponsesSelected)) {
            foreach ($projectResponsesSelected as $projectResponse) {
                // change the projectResponse isSelected to false
                $uow->scheduleExtraUpdate($projectResponse, array(
                        'isSelected' => array(true, false)
                    ));

                // push a message
                $this->session->getFlashBag()->add(
                    'sonata_flash_success',
                    sprintf('La réponse "%s" a été dessélectionnée !', $projectResponse->getShortDescription())
                );
            }
        }
    }
}
