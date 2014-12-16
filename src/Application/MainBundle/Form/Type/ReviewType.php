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
            ->add('rank', null, array(
                'label' => 'Your rating',
                'required' => true,
            ))
            ->add('review', null, array(
                'label' => 'Your review',
            ))
            ->add('personalNote', null, array(
                'label' => 'Your personal note',
                'required' => false,
            ))
            ->add('when', null, array(
                'widget' => 'single_text',
                'label' => 'When did you eat or drink this?',
            ))
            ->add('photoFile', 'file', array(
                'label' => 'Photo',
                'required' => false,
                'attr' => array("accept" => "image/*"),
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
