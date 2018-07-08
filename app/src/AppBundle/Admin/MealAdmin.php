<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class MealAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text', ['label' => 'Nom', 'required' => true]);
        $formMapper->add('description', 'textarea', ['label' => 'Description', 'required' => true]);
        $formMapper->add('price', null, ['label' => 'Prix (€)', 'required' => true]);
        $formMapper->add('availability', null, ['label' => 'Disponible', 'required' => false]);
        $formMapper->add('restaurant', null, ['label' => 'Restaurant', 'required' => true]);
        $formMapper->add('categories', null, ['label' => 'Categories', 'required' => false]);
        $formMapper->add('status', null, ['label' => 'Actif', 'required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id');
        $datagridMapper->add('name');
        $datagridMapper->add('restaurant');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id', null, ['label' => 'ID'])
            ->add('name', null, ['label' => 'Nom'])
            ->addIdentifier('restaurant', null, ['label' => 'Restaurant'])
            ->add('status', null, ['label' => 'Actif', 'editable' => true]);
    }
}