<?php

namespace Claroline\ExternalSynchronizationBundle\Listener;

use Claroline\CoreBundle\Event\User\MergeUsersEvent;
use Claroline\ExternalSynchronizationBundle\Manager\ExternalUserManager;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class ApiListener.
 *
 * @DI\Service
 */
class ApiListener
{
    /** @var ExternalUserManager */
    private $externalUserManager;

    /**
     * @DI\InjectParams({
     *     "externalUserManager" = @DI\Inject("claroline.manager.external_user_manager")
     * })
     *
     * @param ExternalUserManager $externalUserManager
     */
    public function __construct(ExternalUserManager $externalUserManager)
    {
        $this->externalUserManager = $externalUserManager;
    }

    /**
     * @DI\Observe("merge_users")
     *
     * @param MergeUsersEvent $event
     */
    public function onMerge(MergeUsersEvent $event)
    {
        // Replace user of ExternalUser nodes
        $externalUserCount = $this->externalUserManager->replaceUser($event->getRemoved(), $event->getKept());
        $event->addMessage("[ClarolineExternalSynchronizationBundle] updated ExternalUser count: $externalUserCount");
    }
}
