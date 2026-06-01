<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Entity\User;
use App\Form\SellerType;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/seller')]
final class SellerController extends AbstractController
{
    #[Route(name: 'app_seller_index', methods: ['GET'])]
    public function index(
        Request $request,
        SellerRepository $sellerRepository,
        PaginatorInterface $paginator
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $query = $sellerRepository->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->getQuery();

        $sellers = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('seller/index.html.twig', [
            'sellers' => $sellers,
        ]);
    }

    #[Route('/new', name: 'app_seller_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $seller = new Seller();

        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seller);
            $entityManager->flush();

            $this->addFlash('success', 'Vendeur créé avec succès.');

            return $this->redirectToRoute(
                'app_seller_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('seller/new.html.twig', [
            'seller' => $seller,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/create-login', name: 'app_seller_create_login', methods: ['POST'])]
    public function createLogin(
        Seller $seller,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($seller->getUser()) {
            $this->addFlash('warning', 'Ce vendeur possède déjà un compte.');

            return $this->redirectToRoute(
                'app_seller_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        $sellerId = $seller->getId();

        $firstname = strtolower($seller->getFirstNameSeller());
        $firstname = str_replace(' ', '', $firstname);

        $email = $firstname . '.vendeur' . $sellerId . '@test.com';

        $plainPassword = 'seller' . $sellerId;

        $user = new User();

        $user->setEmail($email);
        $user->setRoles(['ROLE_SELLER']);
        $user->setFirstname($seller->getFirstNameSeller());
        $user->setLastname($seller->getLastNameSeller());

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plainPassword
        );

        $user->setPassword($hashedPassword);

        $seller->setUser($user);

        $entityManager->persist($user);
        $entityManager->persist($seller);
        $entityManager->flush();

        $emailMessage = (new Email())
            ->from('admin@minifacturier.com')
            ->to($email)
            ->subject('Votre compte vendeur MiniFacturier')
            ->text(
                "Bonjour " . $seller->getFirstNameSeller() . ",\n\n" .
                "Votre compte vendeur a été créé.\n\n" .
                "Email : " . $email . "\n" .
                "Mot de passe : " . $plainPassword . "\n\n" .
                "Vous pouvez maintenant vous connecter."
            );

        $mailer->send($emailMessage);

        $this->addFlash(
            'success',
            'Compte vendeur créé avec succès. Les identifiants ont été envoyés dans Mailpit.'
        );

        return $this->redirectToRoute(
            'app_seller_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    #[Route('/{id}/resend-login', name: 'app_seller_resend_login', methods: ['GET'])]
    public function resendLogin(
        Seller $seller,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $seller->getUser();

        if (!$user) {
            $this->addFlash(
                'danger',
                'Ce vendeur ne possède pas encore de compte de connexion.'
            );

            return $this->redirectToRoute(
                'app_seller_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        $plainPassword = 'seller' . $seller->getId();

        $emailMessage = (new Email())
            ->from('admin@minifacturier.com')
            ->to($user->getEmail())
            ->subject('Rappel de vos identifiants MiniFacturier')
            ->text(
                "Bonjour " . $seller->getFirstNameSeller() . ",\n\n" .
                "Voici vos identifiants de connexion :\n\n" .
                "Email : " . $user->getEmail() . "\n" .
                "Mot de passe : " . $plainPassword . "\n\n" .
                "Vous pouvez maintenant vous connecter."
            );

        $mailer->send($emailMessage);

        $this->addFlash(
            'success',
            'Les identifiants ont été renvoyés dans Mailpit.'
        );

        return $this->redirectToRoute(
            'app_seller_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    #[Route('/{id}', name: 'app_seller_show', methods: ['GET'])]
    public function show(Seller $seller): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Seller $seller,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Vendeur modifié avec succès.');

            return $this->redirectToRoute(
                'app_seller_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('seller/edit.html.twig', [
            'seller' => $seller,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Seller $seller,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid(
            'delete' . $seller->getId(),
            $request->getPayload()->getString('_token')
        )) {
            $this->addFlash(
                'danger',
                'Jeton de sécurité invalide. Suppression refusée.'
            );

            return $this->redirectToRoute(
                'app_seller_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        if ($seller->getInvoices()->count() > 0) {
            $this->addFlash(
                'danger',
                'Suppression impossible : ce vendeur est lié à une ou plusieurs factures.'
            );

            return $this->redirectToRoute(
                'app_seller_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        $user = $seller->getUser();

        if ($user) {
            $seller->setUser(null);
            $entityManager->remove($user);
        }

        $entityManager->remove($seller);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Vendeur supprimé avec succès.'
        );

        return $this->redirectToRoute(
            'app_seller_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}