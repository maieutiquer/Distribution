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

interface AdapterInterface
{
    public function adapt($object, $to);
    public function getInterface();
    public function getDocumentClass();
    public function getEntityClass();
    public function fromMongo($log);
    public function fromMysql($log);
}