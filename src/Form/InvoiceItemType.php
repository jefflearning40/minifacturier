<?php

namespace App\Form;

use App\Entity\InvoiceItem;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => function (Product $product) {
                    return $product->getName() . ' - ' . $product->getBrand() . ' - ' . $product->getPrice() . ' €';
                },
                'label' => 'Produit',
                'placeholder' => 'Choisissez un produit',
                'required' => false,        // ✅ important
                'empty_data' => null,       // ✅ important
            ])
           
            ->add('price', null, [
                'label' => 'Prix unitaire',
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('quantity', null, [
                'label' => 'Quantité',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceItem::class,
        ]);
    }
}