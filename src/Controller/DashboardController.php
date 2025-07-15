<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Controller;

use Exception;
use Spyck\VisualizationBundle\Entity\Dashboard;
use Spyck\VisualizationBundle\Event\CacheForDashboardEvent;
use Spyck\VisualizationBundle\Utility\DataUtility;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsController]
final class DashboardController extends AbstractController
{
    /**
     * @throws AccessDeniedException
     * @throws Exception
     */
    public function cacheAction(EventDispatcherInterface $eventDispatcher): Response
    {
        $this->admin->checkAccess('list');

        $widget = $this->admin->getSubject();

        DataUtility::assert($widget instanceof Dashboard, $this->createNotFoundException('Dashboard not found'));

        $cacheForDashboardEvent = new CacheForDashboardEvent($widget);

        $eventDispatcher->dispatch($cacheForDashboardEvent);

        $this->addFlash('sonata_flash_success', sprintf('Cache of dashboard "%s" has been cleared.', $widget));

        return $this->redirectToList();
    }
}
