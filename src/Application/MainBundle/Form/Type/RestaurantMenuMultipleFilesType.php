<?php

namespace Application\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class RestaurantMenuMultipleFilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', array(
                'multiple' => true,
//                "attr" => array("accept" => "image/*"),
                'constraints' => array(
                    new All(array(
                        'constraints' => array(
                            new Image(array(
                                'mimeTypes' => array('image/png', 'image/jpeg', 'image/pjpeg'),
                                'mimeTypesMessage' => 'Please upload a jpg or a png photo'
                            )),
                        ))
                    )
                )
            ))
        ;
    }

    public function getName()
    {
        return 'application_main_restaurant_menu_multiple_files';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }
}
