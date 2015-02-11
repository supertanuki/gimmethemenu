<?php

namespace Application\MainBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // remove username ; we use email as the username
        $builder
            ->remove('username')
            ->remove('current_password');

        // custom users fields
        $builder
            ->add('firstName', null, array(
                'label' => 'My first name'
            ))
            ->add('isTimelinePublic', null, array(
                'label' => 'My timeline is public',
                'required' => false
            ))
        ;
    }

    public function getName()
    {
        return 'application_main_user_profile';
    }
}
