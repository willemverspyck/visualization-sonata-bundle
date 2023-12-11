<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Controller;

use Exception;
use Spyck\VisualizationBundle\Entity\Mail;
use Spyck\VisualizationBundle\Service\MailService;
use Spyck\VisualizationBundle\Utility\DataUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsController]
final class MailController extends AbstractController
{
    /**
     * @throws AccessDeniedException
     * @throws Exception
     */
    public function sendAction(MailService $mailService, Request $request): Response
    {
        $this->admin->checkAccess('list');

        $mail = $this->admin->getSubject();

        DataUtility::assert($mail instanceof Mail, $this->createNotFoundException('Unable to find the mail'));

        if (Request::METHOD_POST === $request->getMethod()) {
            $this->validateCsrfToken($request, MailController::class);

            $mailService->handleMailMessageByMail($mail);

            $this->addFlash('sonata_flash_success', sprintf('Mail "%s" has been send to users.', $mail));

            return $this->redirectToList();
        }

        return $this->render('@SpyckVisualizationSonata/mail/send.html.twig', [
            'mail' => $mail,
            'token' => $this->getCsrfToken(MailController::class),
        ]);
    }
}
