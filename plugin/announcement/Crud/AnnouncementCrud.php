<?php

namespace Claroline\AnnouncementBundle\Crud;

use Claroline\AnnouncementBundle\Entity\AnnouncementSend;
use Claroline\AnnouncementBundle\Manager\AnnouncementManager;
use Claroline\AppBundle\Event\Crud\CreateEvent;
use Claroline\AppBundle\Event\Crud\DeleteEvent;
use Claroline\AppBundle\Event\StrictDispatcher;
use Claroline\AppBundle\Persistence\ObjectManager;

class AnnouncementCrud
{
    /**
     * AnnouncementManager constructor.
     *
     * @param StrictDispatcher $eventDispatcher
     */
    public function __construct(
        ObjectManager $om,
        StrictDispatcher $eventDispatcher,
        AnnouncementManager $manager
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->manager = $manager;
        $this->om = $om;
    }

    /**
     * @param CreateEvent $event
     */
    public function preCreate(CreateEvent $event)
    {
        $announcement = $event->getObject();
        $options = $event->getOptions();
        $announcement->setAggregate($options['announcement_aggregate']);
    }

    /**
     * @param CreateEvent $event
     */
    public function preDelete(DeleteEvent $event)
    {
        $announcement = $event->getObject();
        $send = $this->om->getRepository(AnnouncementSend::class)->findBy(['announcement' => $announcement]);

        foreach ($send as $el) {
            $this->om->remove($el);
        }

        // delete scheduled task is any
        $this->manager->unscheduleMessage($announcement);
    }
}
