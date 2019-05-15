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

        $config = $this->container->get('claroline.config.platform_config_handler');

        if ($config->getParameter('mercure.enabled')) {
            $hubUrl = $config->getParameter('mercure.hub_url');
            $jwt = $config->getParameter('mercure.jwt');

            //this can also go in a crud event. We can pretty much c/c all of that as long as we have the data from the $response->getContent()
            //we might want to use uuid later on
            $topic = 'http://localhost/'.$this->getName().'/'.$id;

            $publisher = new Publisher(
              $hubUrl.'?topic='.$topic,
              new StaticJwtProvider($jwt)
            );

            $update = new Update(
              $topic,
              //might want to use response->getContent() sometimes
              $response->getContent()
          );

            // The Publisher service is an invokable object
            $publisher($update);
        }

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
