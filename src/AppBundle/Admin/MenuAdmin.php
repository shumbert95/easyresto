<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class MenuAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text', ['label' => 'Nom', 'required' => true]);
        $formMapper->add('description', 'textarea', ['label' => 'Description', 'required' => true]);
        $formMapper->add('price', null, ['label' => 'Prix (€)', 'required' => true]);
        $formMapper->add('availability', null, ['label' => 'Disponnible', 'required' => false]);
        $formMapper->add('category', null, ['label' => 'Category', 'required' => false]);
        $formMapper->add('restaurant', null, ['label' => 'Restaurant', 'required' => true]);
        $formMapper->add('meals', null, ['label' => 'Plats', 'required' => true]);
        $formMapper->add('status', null, ['label' => 'Actif', 'required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id');
        $datagridMapper->add('name');
        $datagridMapper->add('restaurant');
        $datagridMapper->add('category');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id', null, ['label' => 'ID'])
            ->add('name', null, ['label' => 'Nom'])
            ->addIdentifier('restaurant', null, ['label' => 'Restaurant'])
            ->add('category', null, ['label' => 'Category'])
            ->add('status', null, ['label' => 'Actif', 'editable' => true]);
    }
}