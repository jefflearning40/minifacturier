<?php

namespace App\Form;

use App\Entity\Seller;
use App\Repository\SellerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('seller', EntityType::class, [
                'class' => Seller::class,

                // ==========================
                // AJOUT IMPORTANT
                // ==========================
                // Affiche uniquement les vendeurs
                // qui n'ont pas encore de compte
                'query_builder' => function (SellerRepository $repository) {
                    return $repository->createQueryBuilder('s')
                        ->where('s.user IS NULL')
                        ->orderBy('s.firstNameSeller', 'ASC');
                },
                // ==========================

                'choice_label' => function (Seller $seller) {
                    return $seller->getFirstNameSeller()
                        . ' '
                        . $seller->getLastNameSeller();
                },

                'label' => 'Vendeur',
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])

            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}