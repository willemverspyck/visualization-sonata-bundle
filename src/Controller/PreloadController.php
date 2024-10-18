<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Controller;

use Exception;
use Spyck\VisualizationBundle\Entity\Preload;
use Spyck\VisualizationBundle\Event\PreloadEvent;
use Spyck\VisualizationBundle\Utility\DataUtility;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsController]
final class PreloadController extends AbstractController
{
    /**
     * @throws AccessDeniedException
     * @throws Exception
     */
    public function messageAction(EventDispatcherInterface $eventDispatcher): Response
    {
        $this->admin->checkAccess('list');

        $preload = $this->admin->getSubject();

        DataUtility::assert($preload instanceof Preload, $this->createNotFoundException('Preload not found'));

        $preloadEvent = new PreloadEvent($preload);

        $eventDispatcher->dispatch($preloadEvent);

        $this->addFlash('sonata_flash_success', sprintf('Preload message for "%s".', $preload));

        return $this->redirectToList();
    }
}
