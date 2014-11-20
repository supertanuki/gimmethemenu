<?php

namespace Application\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC', // reverse order (default = 'ASC')
        '_sort_by' => 'rank'  // name of the ordered field
    );

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('rank')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('rank')
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
