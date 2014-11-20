<?php

namespace TE\Bundle\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PageAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('isEnabled')
            ->add('title')
            ->add('slug')
            ->add('seoTitle')
            ->add('seoDescription')
            ->add('body', 'sonata_formatter_type', array(
                'event_dispatcher'      => $formMapper->getFormBuilder()->getEventDispatcher(),
                'format_field'          => 'bodyFormatter',
                'source_field'          => 'rawBody',
                'source_field_options'  => array(
                    'attr' => array('class' => 'span10', 'rows' => 60)
                ),
                'listener'              => true,
                'target_field'          => 'body',
                'ckeditor_context'      => 'default'
            ))

        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('slug')
            ->add('isEnabled')
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
