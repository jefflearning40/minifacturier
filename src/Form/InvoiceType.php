<?php

namespace App\Form;

use App\Entity\customer;
use App\Entity\Invoice;
use App\Entity\seller;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberInvoice')
            ->add('descriptionItem')
            ->add('marque')
            ->add('priceItem')
            ->add('qty')
            ->add('total')
            ->add('saleDate')
            ->add('seller', EntityType::class, [
                'class' => seller::class,
                'choice_label' => 'id',
            ])
            ->add('customer', EntityType::class, [
                'class' => customer::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
