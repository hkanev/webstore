<?php

namespace AdministrationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdministrationBundle\Entity\Discount;

class DiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('discount')
            ->add('startDate')
            ->add('endDate');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Discount::class
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'webstore_bundle_discount_type';
    }
}
