<?php

namespace Application\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DishGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'Meal\'s name'
            ))
            ->add('price', null, array(
                'label' => 'Meal\'s price'
            ))
            ->add('dishes', 'collection', array(
                'label' => false,
                'type' => new DishChildrenReviewType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'cascade_validation' => true,
            ))
        ;
    }

    public function getName()
    {
        return 'application_main_dish_group';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Application\MainBundle\Entity\Dish',
                'cascade_validation' => true,
            ));
    }
}
