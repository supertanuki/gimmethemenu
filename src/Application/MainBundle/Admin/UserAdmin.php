<?php

namespace Application\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'createdAt'  // name of the ordered field
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('firstName')
            ->add('lastName')
            ->add('address')
            ->add('postalCode')
            ->add('city')
            ->add('country')
            ->add('phone')
            ->add('locked', null, array('required' => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('locked')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email')
            ->add('firstName')
            ->add('lastName')
            ->add('city')
            ->add('locked')
            ->add('lastLogin')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    public function getBatchActions()
    {
        return array();
    }

    public function getExportFormats()
    {
        return array();
    }
}
