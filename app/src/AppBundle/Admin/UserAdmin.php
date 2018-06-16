<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class UserAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('firstName', 'text', ['label' => 'Prénom', 'required' => true]);
        $formMapper->add('lastName', 'text', ['label' => 'Nom de famille', 'required' => true]);
        $formMapper->add('username', 'text', ['label' => 'Nom d\'utilisateur', 'required' => true]);
        $formMapper->add('email', 'text', ['label' => 'Email', 'required' => true]);
        $formMapper->add('type', 'choice', ['choices' => [
            'Client'        => '1',
            'Employé'       => '2',
            'Restaurateur'  => '3'
        ], 'required' => true]);
        $formMapper->add('plainPassword', 'text', ['label' => 'Mot de passe', 'required' => true]);
        $formMapper->add('enabled', null, ['label' => 'Actif', 'required' => true]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id');
        $datagridMapper->add('firstName');
        $datagridMapper->add('lastName');
        $datagridMapper->add('email');
        $datagridMapper->add('type');
        $datagridMapper->add('enabled');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id', null, ['label' => 'ID'])
            ->add('firstName', null, ['label' => 'Prénom'])
            ->add('lastName', null, ['label' => 'Nom de famille'])
            ->add('email', null, ['label' => 'Email'])
            ->add('type', null, ['label' => 'Type'])
            ->add('enabled', null, ['label' => 'Actif', 'editable' => true]);
    }
}