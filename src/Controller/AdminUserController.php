<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminUserController extends AbstractController
{

    #[Route(
        '/create-seller-user',
        name:'app_admin_create_seller_user'
    )]
    public function index(): Response
    {

        return $this->redirectToRoute(
            'app_seller_index'
        );

    }

}