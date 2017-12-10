<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class RestaurantAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text', ['label' => 'Nom', 'required' => true]);
        $formMapper->add('address', 'text', ['label' => 'Adresse', 'required' => true]);
        $formMapper->add('addressComplement', 'text', ['label' => 'Complément d\'adresse', 'required' => false]);
        $formMapper->add('postalCode', 'text', ['label' => 'Code postal', 'required' => true]);
        $formMapper->add('city', 'text', ['label' => 'Ville', 'required' => true]);
        $formMapper->add('description', 'textarea', ['label' => 'Description', 'required' => true]);
        $formMapper->add('open', null, ['label' => 'Ouvert', 'required' => true]);
        $formMapper->add('users', null, ['label' => 'Employés', 'required' => false]);
        $formMapper->add('schedule', 'textarea', ['label' => 'Horaires', 'required' => false]);
        $formMapper->add('status', null, ['label' => 'Actif', 'required' => true]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id');
        $datagridMapper->add('name');
        $datagridMapper->add('open');
        $datagridMapper->add('city');
        $datagridMapper->add('postalCode');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id', null, ['label' => 'ID'])
            ->add('name', null, ['label' => 'Nom'])
            ->add('city', null, ['label' => 'Ville'])
            ->add('postalCode', null, ['label' => 'Code postal'])
            ->add('status', null, ['label' => 'Actif', 'editable' => true]);
    }
}