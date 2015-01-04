<?php
namespace Application\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;

class FollowersNotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('followers:notification')
            ->setDescription('Send to users their new followers.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $output->writeln('Begin FollowersNotification!');

        $dispatcher = $this->getContainer()->get('hip_mandrill.dispatcher');

        $users = $doctrine
            ->getRepository('ApplicationMainBundle:User')
            ->getLatestFollowers();

        foreach ($users as $user) {

            // disable notification email
            foreach ($user->getFollowers() as $follower) {
                $follower->setIsNotificationEmail(true);
                $em->persist($follower);
            }

            $em->flush();

            $message = new Message();
            $message
//                ->addTo('supertanuki@gmail.com')
                ->addTo($user->getEmail())
                ->setSubject(sprintf("%s, you have new followers on GimmeTheMenu", $user))
                ->setHtml($this->getContainer()->get('templating')->render(
                    'ApplicationMainBundle:Email:follower.html.twig',
                    array('user' => $user)
                ));

            $results = $dispatcher->send($message);

            foreach ($results as $result) {
                if(is_array($result)) {
                    $output->writeln(sprintf(
                        "Email %s is %s with id=%s" . ($result['reject_reason'] ? " (reject with this reason: %s)" : " %s"),
                        $result['email'],
                        $result['status'],
                        $result['_id'],
                        $result['reject_reason']
                    ));
                }
            }
        }

        $output->writeln('End FollowersNotification!');
    }
}