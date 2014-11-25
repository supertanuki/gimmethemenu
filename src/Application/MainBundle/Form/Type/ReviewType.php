<?php

namespace Application\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('review')
            ->add('rank', null, array(
                'label' => 'Rating'
            ))
            ->add('when', null, array(
                'widget' => 'single_text',
                'label' => 'When did you eat this dish?'
            ))
            ->add('photoFile', 'file', array(
                'label' => 'Photo'
            ))
        ;
    }

    public function getName()
    {
        return 'application_main_review';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Application\MainBundle\Entity\Review',
            ));
    }
}
