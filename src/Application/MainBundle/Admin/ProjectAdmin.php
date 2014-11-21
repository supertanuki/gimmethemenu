<?php

namespace Application\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;

use Application\MainBundle\Entity\Project;

class ProjectAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'createdAt'  // name of the ordered field
    );

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Project caracteristics')
                ->add('title')

                ->add('category', null, array('label' => 'Catégorie'))

                ->add('status', 'choice', array(
                        'choices' => Project::$STATUS_LABEL
                    ))

                ->add('endAt')
                ->add('budget')
            ->end()

            ->with('Description and files')
                ->add('summary')

                ->add('description', 'sonata_formatter_type', array(
                        'event_dispatcher'      => $formMapper->getFormBuilder()->getEventDispatcher(),
                        'format_field'          => 'descriptionFormatter',
                        'source_field'          => 'rawDescription',
                        'source_field_options'  => array(
                            'attr' => array('class' => 'span10', 'rows' => 30)
                        ),
                        'listener'              => true,
                        'target_field'          => 'description',
                        'ckeditor_context'      => 'default'
                    ))

                ->add('projectFiles', 'sonata_type_collection', array(
                        'by_reference' => false,
                    ), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                    ))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('status', 'doctrine_orm_string', array(), 'choice', array(
                    'choices' => Project::$STATUS_LABEL
                ))
            ->add('category')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('category')
            ->add('statusLabel')
            ->add('ProjectResponsesCount', 'url', array(
                'route' => array(
                        'name' => 'admin_application_main_project_projectresponse_list',
                        'identifier_parameter_name' => 'id'
                    )
                )
            )
            ->add('endAt')
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

    public function prePersist($project)
    {
        $this->preUpdate($project);
    }

    public function preUpdate($project)
    {
        $project->setProjectFiles($project->getProjectFiles());
    }

    /*
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if ($this->isChild() || !$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $id = $this->getRequest()->get('id');

        $menu->addChild(
            'Voir les propositions',
            array(
                'uri' => $this->generateUrl(
                    'sonata.admin.project.response.list',
                    array('id' => $id)
                )
            )
        );
    }
    */
}
