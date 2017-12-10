<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class CategoryMealMenuAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text', ['label' => 'Code', 'required' => true]);
        $formMapper->add('name', 'text', ['label' => 'Nom', 'required' => true]);
        $formMapper->add('status', null, ['label' => 'Actif', 'required' => true]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id');
        $datagridMapper->add('code');
        $datagridMapper->add('name');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id', null, ['label' => 'ID'])
            ->add('code', null, ['label' => 'Code'])
            ->add('name', null, ['label' => 'Nom'])
            ->add('status', null, ['label' => 'Actif']);
    }
}