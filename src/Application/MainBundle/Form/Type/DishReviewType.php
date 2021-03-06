<?php

namespace Application\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DishReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('dishType', null, array(
                'required' => true
            ))
            ->add('reviews', 'collection', array(
                'label' => false,
                'type' => new ReviewType(),
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'application_main_dish_review';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Application\MainBundle\Entity\Dish',
                'cascade_validation' => true,
            ));
    }
}
