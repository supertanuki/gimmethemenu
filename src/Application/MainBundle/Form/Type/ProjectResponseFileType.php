<?php

namespace Application\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectResponseFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('documentFile', 'vich_file', array(
                    'required'      => false,
                    'mapping'       => 'project_response_file',
                    'allow_delete'  => false,
                    'download_link' => false,
                ))
            ;
    }

    public function getName()
    {
        return 'te_main_project_response_file';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Application\MainBundle\Entity\ProjectResponseFile',
            ));
    }
}
