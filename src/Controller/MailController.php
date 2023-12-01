<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Controller;

use App\Utility\DataUtility;
use Spyck\VisualizationBundle\Entity\Mail;
use Spyck\VisualizationBundle\Service\MailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

#[AsController]
final class MailController extends AbstractController
{
    /**
     * @throws AccessDeniedException
     * @throws Throwable
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

        return $this->renderWithExtraParams('@SpyckVisualizationSonata/mail/send.html.twig', [
            'mail' => $mail,
            'token' => $this->getCsrfToken(MailController::class),
        ]);
    }
}
