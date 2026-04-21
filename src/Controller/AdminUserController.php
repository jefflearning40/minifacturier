<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SellerUserType;
use App\Repository\SellerRepository;
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

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_SELLER']);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['password']
            );

            $user->setPassword($hashedPassword);

            $seller = $data['seller'];

            
            $seller->setUser($user);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte vendeur créé avec succès.');

            return $this->redirectToRoute('app_seller_index');
        }

        return $this->render('admin/create_seller_user.html.twig', [
            'form' => $form,
        ]);
    }
}