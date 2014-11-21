<?php

namespace Application\MainBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\True;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // remove username ; we use email as the username
        $builder->remove('username');

        // custom users fields
        $builder
            ->add('firstName')
//            ->add('lastName')
//            ->add('address')
//            ->add('postalCode')
//            ->add('city')
//            ->add('country')
//            ->add('phone')
            ->add('recaptcha', 'ewz_recaptcha', array(
                    'attr' => array(
                        'options' => array(
                            'theme' => 'clean'
                        )
                    ),
                    'mapped' => false,
                    'constraints' => array(
                        new True()
                    )
                ))
        ;
    }

    public function getName()
    {
        return 'application_main_user_registration';
    }
}
