<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SellerUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
class AdminUserController extends AbstractController
{
    #[Route('/create-seller-user', name: 'app_admin_create_seller_user')]
    public function createSellerUser(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(SellerUserType::class);

        $form->handleRequest($request);

        // Vérifie si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // =========================
            // TEST TEMPORAIRE
            // =========================
            dd('FORMULAIRE VALIDE', $form->getData());

            // =========================
            // CODE NORMAL
            // =========================
            $data = $form->getData();

            $seller = $data['seller'];

            $user = new User();

            $user->setEmail($data['email']);

            $user->setRoles(['ROLE_SELLER']);

            $user->setFirstname($seller->getFirstNameSeller());

            $user->setLastname($seller->getLastNameSeller());

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['password']
            );

            $user->setPassword($hashedPassword);

            $seller->setUser($user);

            $em->persist($user);

            $em->persist($seller);

            $em->flush();

            $this->addFlash(
                'success',
                'Compte vendeur créé avec succès.'
            );

            return $this->redirectToRoute('app_seller_index');
        }

        return $this->render('admin/create_seller_user.html.twig', [
            'form' => $form,
        ]);
    }
}