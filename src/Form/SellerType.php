<?php

namespace App\Form;

use App\Entity\Seller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstNameSeller', null, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Entrez le prénom'
                ]
            ])
            ->add('lastNameSeller', null, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Entrez le nom'
                ]
            ])
            ->add('phoneSeller', null, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Entrez le numéro'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}