<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Controller;

use Exception;
use Spyck\VisualizationBundle\Entity\Widget;
use Spyck\VisualizationBundle\Event\CacheEvent;
use Spyck\VisualizationBundle\Utility\DataUtility;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsController]
final class WidgetController extends AbstractController
{
    /**
     * @throws AccessDeniedException
     * @throws Exception
     */
    public function cacheAction(EventDispatcherInterface $eventDispatcher): Response
    {
        $this->admin->checkAccess('list');

        $widget = $this->admin->getSubject();

        DataUtility::assert($widget instanceof Widget, $this->createNotFoundException('Widget not found'));

        $cacheEvent = new CacheEvent($widget);

        $eventDispatcher->dispatch($cacheEvent);

        $this->addFlash('sonata_flash_success', sprintf('Cache of Widget "%s" has been cleared.', $widget));

        return $this->redirectToList();
    }
}
