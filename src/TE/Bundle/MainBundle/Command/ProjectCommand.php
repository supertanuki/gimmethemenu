<?php
namespace TE\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('project:close-ended')
            ->setDescription('Change status to closed of published projects which have dateEnd < now')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = $this->getContainer()->get('te_main.project_close_ended');
        $out = $updater->closeEndedProjects();
        $output->writeln($out);
    }
}