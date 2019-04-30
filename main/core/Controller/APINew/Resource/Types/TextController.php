<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Controller\APINew\Resource\Types;

use Claroline\AppBundle\Annotations\ApiDoc;
use Claroline\AppBundle\Controller\AbstractCrudController;
use Claroline\CoreBundle\Entity\Resource\Text;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Jwt\StaticJwtProvider;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

define('HUB_URL', 'http://localhost:3000/hub');
define('JWT', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.iHLdpAEjX4BqCsHJEegxRmO-Y6sMxXwNATrQyRNt3GY');

/**
 * @EXT\Route("resource_text")
 */
class TextController extends AbstractCrudController
{
    /**
     * @ApiDoc(
     *     description="Update an object class $class.",
     *     body={
     *         "schema":"$schema"
     *     },
     *     parameters={
     *         "id": {
     *              "type": {"string", "integer"},
     *              "description": "The object id or uuid"
     *          }
     *     }
     * )
     *
     * @param string|int $id
     * @param Request    $request
     * @param string     $class
     *
     * @return JsonResponse
     */
    public function updateAction($id, Request $request, $class)
    {
        $response = parent::updateAction($id, $request, $class);

        var_dump(JWT);
        var_dump(HUB_URL);

        $publisher = new Publisher(
          HUB_URL."?topic={$id}",
          new StaticJwtProvider(JWT)
        );

        //get url from referer.
        $update = new Update(
            'http://localhost/Claroline/Claroline/web/app_dev.php/resources/show/text/7EEC1A2A-F204-4EDF-A158-98CE49B4E00F#/edit',
            $response->getContent()
        );

        // The Publisher service is an invokable object
        $publisher($update);

        return $response;
    }

    public function getClass()
    {
        return Text::class;
    }

    public function getIgnore()
    {
        return ['create', 'exist', 'list', 'copyBulk', 'deleteBulk', 'schema', 'find', 'get'];
    }

    public function getName()
    {
        return 'resource_text';
    }
}
