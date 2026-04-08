<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\Seller;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                    'placeholder' => 'Entrez le numéro de facture'
                ]
            ])
            ->add('descriptionItem', null, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Entrez la description'
                ]
            ])
            ->add('marque', null, [
                'label' => 'Marque',
                'attr' => [
                    'placeholder' => 'Entrez la marque'
                ]
            ])
            ->add('priceItem', null, [
                'label' => 'Prix unitaire',
                'attr' => [
                    'placeholder' => 'Entrez le prix unitaire'
                ]
            ])
            ->add('qty', null, [
                'label' => 'Quantité',
                'attr' => [
                    'placeholder' => 'Entrez la quantité'
                ]
            ])
            ->add('total', null, [
                'label' => 'Total',
                'attr' => [
                    'placeholder' => 'Entrez le total'
                ]
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}