<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\Seller;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberInvoice', null, [
                'label' => 'Numéro de facture',
                'attr' => [
                    'placeholder' => 'Entrez le numéro de facture',
                ],
            ])
            ->add('saleDate', null, [
                'label' => 'Date de vente',
            ])
            ->add('seller', EntityType::class, [
                'class' => Seller::class,
                'choice_label' => function (Seller $seller) {
                    return $seller->getFirstNameSeller() . ' ' . $seller->getLastNameSeller();
                },
                'label' => 'Vendeur',
                'placeholder' => 'Choisissez un vendeur',
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => function (Customer $customer) {
                    return $customer->getFirstNameCustomer() . ' ' . $customer->getLastNameCustomer();
                },
                'label' => 'Client',
                'placeholder' => 'Choisissez un client',
            ])
            ->add('invoiceItems', CollectionType::class, [
                'entry_type' => InvoiceItemType::class,
                'label' => 'Lignes de facture',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}