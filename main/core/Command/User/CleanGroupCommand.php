<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Command\User;

use Claroline\AppBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Entity\Group;
use Claroline\CoreBundle\Entity\User;
use Claroline\CoreBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanGroupCommand extends ContainerAwareCommand
{
    /** @var ObjectManager */
    private $om;
    /** @var UserManager */
    private $manager;

    protected function configure()
    {
        $this
            ->setName('claroline:user:clean_group')
            ->setDescription('Disable users of a group if they are not in a csv file.')
            ->addArgument('csv_path', InputArgument::REQUIRED, 'The absolute path to the csv file containing the users to keep')
            ->addArgument('group', InputArgument::REQUIRED, 'The name of the group');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->om = $this->getContainer()->get(ObjectManager::class);
        $this->manager = $this->getContainer()->get('claroline.manager.user_manager');

        // get group
        $group = $this->om->getRepository(Group::class)->findOneBy(['name' => $input->getArgument('group')]);
        if (!$group) {
            throw new \Exception('Group cannot be found.');
        }

        // get emails from file
        $file = $input->getArgument('csv_path');
        $lines = str_getcsv(file_get_contents($file), PHP_EOL);
        $emails = array_map(function ($line) {
            $email = str_getcsv($line, ';')[0];
            if ($email) {
                return trim($email);
            }
            return '';
        }, $lines);


        /** @var User[] $users */
        $users = $this->om->getRepository(User::class)->findByGroup($group);

        $this->om->startFlushSuite();

        foreach ($users as $i => $user) {
            if (!in_array($user->getEmail(), $emails)) {
                $output->writeln(sprintf('Disable user %s.', $user->getEmail()));

                $this->manager->disable($user);

                if (0 === $i % 200) {
                    $this->om->forceFlush();
                }
            }
        }

        $this->om->flush();
        $this->om->endFlushSuite();
    }
}
