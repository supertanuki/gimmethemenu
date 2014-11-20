<?php

namespace Application\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ProjectFileAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('media', 'sonata_type_model_list',
                array(),
                array('link_parameters' => array('context' => 'Projet'))
            )
        ;
    }
}
