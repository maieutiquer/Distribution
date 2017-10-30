<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Persistence;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("claroline.persistence.adapter")
 */
class AdapterProvider
{
    /**
     * The list of registered adapters in the platform.
     *
     * @var array
     */
    private $adapters = [];

    public function add(AdapterInterface $adapter)
    {
        $this->adapters[$adapter->getInterface()] = $adapter;
    }

    public function has($class)
    {
        return array_key_exists($class, $this->adapters);
    }

    public function get($class)
    {
        if (is_object($class)) {
            $interfaces = class_implements(get_class($class));
        } else {
            $obj = new \ReflectionClass($class);
            $interfaces = $obj->isInterface() ? [$class] : class_implements($class);
        }

        foreach ($this->adapters as $adapter) {
            if (in_array($adapter->getInterface(), $interfaces)) {
                return $adapter;
            }
        }

        throw new AdapterException(
            sprintf('No adapter found for class "%s" Maybe you forgot to add the "claroline.mongodb.adapter" tag to your adapter.', $class)
        );
    }

    public function adapt($object, $to)
    {
        return $this->get($object)->adapt($object, $to);
    }
}