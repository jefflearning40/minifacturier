<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/catalogue', name: 'app_catalog_index')]
    public function catalogue(): Response
    {
        return $this->render('maintenance.html.twig', [
            'title' => 'Catalogue en cours de développement'
        ]);
    }

    #[Route('/statistiques', name: 'app_stats_index')]
    public function stats(): Response
    {
        return $this->render('maintenance.html.twig', [
            'title' => 'Statistiques en cours de développement'
        ]);
    }

    #[Route('/create-users-test', name: 'app_create_users_test')]
    public function createUsersTest(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $repository = $entityManager->getRepository(User::class);

        if (
            $repository->findOneBy(['email' => 'admin@test.com']) ||
            $repository->findOneBy(['email' => 'seller@test.com'])
        ) {
            return new Response('Les utilisateurs de test existent déjà.');
        }

        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstname('Admin');
        $admin->setLastname('Principal');
        $admin->setPassword($passwordHasher->hashPassword($admin, 'admin123'));

        $seller = new User();
        $seller->setEmail('seller@test.com');
        $seller->setRoles(['ROLE_SELLER']);
        $seller->setFirstname('Jean');
        $seller->setLastname('Vendeur');
        $seller->setPassword($passwordHasher->hashPassword($seller, 'seller123'));

        $entityManager->persist($admin);
        $entityManager->persist($seller);
        $entityManager->flush();

        return new Response('Utilisateurs créés avec succès');
    }
}