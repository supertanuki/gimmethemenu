<?php
namespace Application\MainBundle\Service;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Application\MainBundle\Entity\Project;
use Application\MainBundle\Repository\ProjectRepository;

class ProjectCloseEnded
{
    protected $em;
    protected $logger;

    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /*
     * Change status to closed of published projects which have dateEnd < now
     */
    public function closeEndedProjects()
    {
        $outputs = array();
        $projects = $this->getProjectRepository()->getActivedAndEndedProjects();

        if (!count($projects)) {
            return 'Nothing to change !';
        }

        foreach ($projects as $project) {
            $project->setStatus(Project::STATUS_CLOSED);
            $this->em->persist($project);

            $output = sprintf('-> updating status for project "%s"', $project->getTitle());
            $outputs[] = $output;
            $this->getLogger()->info($output);
        }

        $this->em->flush();

        return implode("\n", $outputs);
    }

    /**
     * @return ProjectRepository
     */
    protected function getProjectRepository()
    {
        return $this->em->getRepository('ApplicationMainBundle:Project');
    }
}