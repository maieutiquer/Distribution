<?php

namespace Claroline\CoreBundle\Listener\Administration;

use Claroline\CoreBundle\API\Serializer\ParametersSerializer;
use Claroline\CoreBundle\Event\OpenAdministrationToolEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;

/**
 * Workspace administration tool.
 *
 * @DI\Service()
 */
class WorkspaceListener
{
    /** @var TwigEngine */
    private $templating;

    /** @var ParametersSerializer */
    private $parametersSerializer;

    /**
     * WorkspaceListener constructor.
     *
     * @DI\InjectParams({
     *     "parametersSerializer" = @DI\Inject("claroline.serializer.parameters"),
     *     "templating"           = @DI\Inject("templating")
     * })
     *
     * @param TwigEngine $templating
     */
    public function __construct(
        TwigEngine $templating,
        ParametersSerializer $parametersSerializer
    ) {
        $this->templating = $templating;
        $this->parametersSerializer = $parametersSerializer;
    }

    /**
     * Displays workspace administration tool.
     *
     * @DI\Observe("administration_tool_workspace_management")
     *
     * @param OpenAdministrationToolEvent $event
     */
    public function onDisplayTool(OpenAdministrationToolEvent $event)
    {
        $content = $this->templating->render(
            'ClarolineCoreBundle:administration:workspaces.html.twig',
            ['parameters' => $this->parametersSerializer->serialize()]
        );

        $event->setResponse(new Response($content));
        $event->stopPropagation();
    }
}