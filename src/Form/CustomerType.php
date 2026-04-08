<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstNameCustomer', null, [
                'label' => 'Prénom'
            ])
            ->add('lastNameCustomer', null, [
                'label' => 'Nom'
            ])
            ->add('addressCustomer', null, [
                'label' => 'Adresse mail'
            ])
            ->add('phoneCustomer', null, [
                'label' => 'Téléphone'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}