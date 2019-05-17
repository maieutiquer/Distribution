<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Controller\Mercure;

use Claroline\AppBundle\Controller\RequestDecoderTrait;
use Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Jwt\StaticJwtProvider;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class MercureController
{
    use RequestDecoderTrait;

    /**
     * @DI\InjectParams({
     *   "platformConfigHandler"    = @DI\Inject("claroline.config.platform_config_handler")
     * })
     *
     * @param PlatformConfigurationHandler $platformConfigHandler
     */
    public function __construct(
        PlatformConfigurationHandler $platformConfigHandler
    ) {
        $this->ch = $platformConfigHandler;
    }

    /**
     * @EXT\Route(
     *     "/publish/{uuid}",
     *     name="claro_mercure_publish",
     *     options={"expose"=true}
     * )
     * @EXT\Method("POST")
     */
    public function publishAction($uuid, Request $request)
    {
        $hubUrl = $this->ch->getParameter('mercure.hub_url');
        $jwt = $this->ch->getParameter('mercure.jwt');
        $storage = $this->ch->getParameter('mercure.storage_folder');

        if (!is_dir($storage)) {
            mkdir($storage);
        }

        $data = $this->decodeRequest($request);
        $topic = "http://localhost/{$uuid}";

        $publisher = new Publisher(
            $hubUrl.'?topic='.$topic,
            new StaticJwtProvider($jwt)
        );

        $encoded = json_encode($data);

        $update = new Update(
            $topic,
            json_encode($data)
        );

        file_put_contents($encoded, $storage.DIRECTORY_SEPARATOR.$uuid);

        // The Publisher service is an invokable object
        $publisher($update);

        return new JsonResponse();
    }

    /**
     * @EXT\Route(
     *     "/get/{uuid}",
     *     name="claro_mercure_get",
     *     options={"expose"=true}
     * )
     */
    public function getAction($uuid)
    {
        $storage = $this->ch->getParameter('mercure.storage_folder');
        $content = null;
        if (file_exists($storage.DIRECTORY_SEPARATOR.$uuid)) {
            $content = file_get_contents($storage.DIRECTORY_SEPARATOR.$uuid);
        }

        return new JsonResponse($content);
    }
}
