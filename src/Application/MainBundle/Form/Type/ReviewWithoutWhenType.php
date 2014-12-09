<?php

namespace Application\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReviewWithoutWhenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rank', null, array(
                'label' => 'Your rating',
            ))
            ->add('review', null, array(
                'label' => 'Your review',
            ))
            ->add('personalNote', null, array(
                'label' => 'Your personal note',
                'required' => false,
            ))
            ->add('photoFile', 'file', array(
                'label' => 'Photo',
                'required' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'application_main_review_without_when';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Application\MainBundle\Entity\Review',
            ));
    }
}
