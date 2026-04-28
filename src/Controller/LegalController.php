<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_legal_mentions')]
    public function mentions(): Response
    {
        return $this->render('legal/mentions.html.twig');
    }

    #[Route('/rgpd', name: 'app_rgpd')]
    public function rgpd(): Response
    {
        return $this->render('legal/rgpd.html.twig');
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $firstname = trim($request->request->get('firstname', ''));
            $lastname = trim($request->request->get('lastname', ''));
            $senderEmail = trim($request->request->get('email', ''));
            $message = trim($request->request->get('message', ''));

            if (
                $firstname === '' ||
                $lastname === '' ||
                $senderEmail === '' ||
                $message === '' ||
                !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)
            ) {
                $this->addFlash('danger', 'Veuillez remplir correctement tous les champs du formulaire.');

                return $this->redirectToRoute('app_contact');
            }

            $email = (new Email())
                ->from('contact@minifacturier.com')
                ->replyTo($senderEmail)
                ->to('admin@minifacturier.com')
                ->subject('Nouveau message de contact')
                ->text(
                    "Nouveau message reçu depuis le formulaire de contact.\n\n" .
                    "Nom : " . $lastname . "\n" .
                    "Prénom : " . $firstname . "\n" .
                    "Email : " . $senderEmail . "\n\n" .
                    "Message :\n" . $message
                );

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('legal/contact.html.twig');
    }
}