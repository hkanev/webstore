<?php

namespace AdministrationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('Checkout', 'Symfony\Component\Form\Extension\Core\Type\ButtonType')
        ->add('Delete', 'Symfony\Component\Form\Extension\Core\Type\ButtonType')
        ->add('Edit', 'Symfony\Component\Form\Extension\Core\Type\ButtonType');
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'administration_bundle_checkout_type';
    }
}
