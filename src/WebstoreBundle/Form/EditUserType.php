<?php

namespace WebstoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                )
            )
            ->add('address', TextareaType::class)
            ->add('telephone', TextType::class)
            ->add('country', TextType::class)
            ->add('city', TextType::class)
            ->add('role', ChoiceType::class, array(
                'choices' => array(
                    'User' => "ROLE_USER",
                    'Admin' => "ROLE_ADMIN",
                    "Editor" => "ROLE_EDITOR"
                ),
                'preferred_choices' => 'ROLE_USER'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebstoreBundle\Entity\User',
        ));
    }

    public function getBlockPrefix()
    {
        return 'webstore_bundle_edit_user_type';
    }
}
