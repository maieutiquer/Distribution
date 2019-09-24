<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Command\Dev;

use Claroline\AppBundle\API\Options;
use Claroline\AppBundle\API\Utils\FileBag;
use Claroline\AppBundle\Command\BaseCommandTrait;
use Claroline\AppBundle\Logger\ConsoleLogger;
use Claroline\CoreBundle\Command\AdminCliCommand;
use Claroline\CoreBundle\Entity\Role;
use Claroline\CoreBundle\Entity\User;
use Claroline\CoreBundle\Entity\Workspace\Workspace;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates an user, optionaly with a specific role (default to simple user).
 */
class MigrateWorkspaceCommand extends ContainerAwareCommand implements AdminCliCommand
{
    use BaseCommandTrait;

    private $params = [
        'url' => 'The base urls from where the workspace will be fetched: ',
        'path' => 'The file base path: ',
        'code' => 'The workspace code:  ',
    ];

    protected function configure()
    {
        $this->setName('claroline:workspace:migrate')
            ->setDescription('Create a workspace from a zip archive (for debug purpose)');
        $this->setDefinition(
            [
                new InputArgument('url', InputArgument::REQUIRED, 'The base urls from where the workspace will be fetched'),
                new InputArgument('path', InputArgument::REQUIRED, 'The file base path'),
                new InputArgument('code', InputArgument::REQUIRED, 'The workspace code'),
                new InputArgument('creator', InputArgument::REQUIRED, 'The creator username'),
            ]
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //get the workspace id from the code
        $data = json_decode(file_get_contents($input->getArgument('url').'/apiv2/workspace/'.$input->getArgument('code').'/export/definition'), true);
        $consoleLogger = ConsoleLogger::get($output);
        $fileBag = new FileBag();

        //we assume everything in on the same server
        foreach ($data['orderedTools'] as $orderedTool) {
            if (isset($orderedTool['data']['resources'])) {
                foreach ($orderedTool['data']['resources'] as &$resource) {
                    if ('file' === $resource['_type']) {
                        $fileBag->add($resource['_path'], $input->getArgument('path').'/'.$resource['hashName']);
                    }
                }
            }
        }

        //check if code already exists and replace it if it's the case
        $om = $this->getContainer()->get('claroline.persistence.object_manager');
        $workspace = $om->getRepository(Workspace::class)->findOneByCode($data['code']);

        if (!$workspace) {
            $workspace = new Workspace();
        }

        $creator = $om->getRepository(User::class)->findOneByUsername($input->getArgument('creator'));
        $workspace->setCreator($creator);
        $this->getContainer()->get('claroline.manager.workspace.transfer')->setLogger($consoleLogger);
        $this->getContainer()->get('claroline.manager.workspace.transfer')->deserialize($data, $workspace, [Options::NO_HASH_REBUILD], $fileBag);
        $managerRole = $om->getRepository(Role::class)->findOneBy(['workspace' => $workspace, 'translationKey' => 'manager']);
        $creator->addRole($managerRole);
        $om->persist($creator);
        $om->flush();
    }
}
