<?php
namespace TE\Bundle\MainBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use TE\Bundle\MainBundle\Entity\ProjectResponseFile;

class ProjectResponseFileListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->updateProjectResponse($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateProjectResponse($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->updateProjectResponse($args);
    }

    /*
     * Update the UpdatedAt datetime of the projectResponse
     * when a projectResponseFile is added, updated or removed
     */
    protected function updateProjectResponse(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof ProjectResponseFile) {
            $projectResponse =  $entity->getProjectResponse();
            $projectResponse->setUpdatedAt(new \DateTime('now'));
            $em->flush();
        }
    }
}
