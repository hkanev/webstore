<?php

namespace WebstoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebstoreBundle\Entity\Review;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class)
            ->add('rating', ChoiceType::class,
                [
                    'choices' =>
                    [
                        1, 2, 3, 4, 5
                    ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'data-class' => Review::class
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'webstore_bundle_review_type';
    }
}
