<?php

namespace Claroline\CoreBundle\Listener\Entity;

use Claroline\AppBundle\Manager\DatabaseManager;
use Claroline\AppBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Entity\Log\Log;
use Claroline\CoreBundle\Entity\Log\LogTable;
use Claroline\CoreBundle\Entity\User;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class MappingListener
{
    public function __construct(DatabaseManager $manager, ObjectManager $om)
    {
        $this->manager = $manager;
        $this->isCreatingTable = false;
        $this->om = $om;
    }

    /**
     * @param User $user
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $table = $classMetadata->table;

        switch ($classMetadata->name) {
          case Log::class:
            //we load metadata in createTable method, if we don't do that, we go infinite
            if ($this->isCreatingTable) {
                return true;
            }

            $year = date('Y');
            $name = 'claro_log_'.$year;
            $table['name'] = $name;

            //we load metadata in createTable method, if we don't do that, we go infinite
            $this->isCreatingTable = true;
            $classMetadata->setPrimaryTable($table);
            //It would be cleaner if we only send the entity clas name & new table name but it's a start

            //check if it exists first
            if ($this->manager->tableExists('claro_log_table')) {
                $logTable = new LogTable();
                $logTable->setYear($year);
                $logTable->setName($name);
                $this->om->persist($logTable);
                $this->om->flush();
            }

            $this->manager->createTable($name, $classMetadata);
        }
    }
}
