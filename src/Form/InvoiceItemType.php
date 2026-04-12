<?php

namespace App\Form;

use App\Entity\InvoiceItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productName', null, [
                'label' => 'Produit',
                'attr' => [
                    'placeholder' => 'Nom du produit',
                ],
            ])
            ->add('brand', null, [
                'label' => 'Marque',
                'attr' => [
                    'placeholder' => 'Marque',
                ],
            ])
            ->add('price', null, [
                'label' => 'Prix unitaire',
                'attr' => [
                    'placeholder' => 'Prix',
                ],
            ])
            ->add('quantity', null, [
                'label' => 'Quantité',
                'attr' => [
                    'placeholder' => 'Quantité',
                ],
            ])
            ->add('total', null, [
                'label' => 'Total',
                'attr' => [
                    'placeholder' => 'Total',
                    'readonly' => true,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceItem::class,
        ]);
    }
}