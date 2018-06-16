<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class ClientAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('address', 'text', ['label' => 'Adresse', 'required' => true]);
        $formMapper->add('addressComplement', 'text', ['label' => 'Complément d\'adresse', 'required' => false]);
        $formMapper->add('postalCode', 'text', ['label' => 'Code postal', 'required' => true]);
        $formMapper->add('city', 'text', ['label' => 'Ville', 'required' => true]);
        $formMapper->add('phone', 'text', ['label' => 'Téléphone', 'required' => true]);
        $formMapper->add('user', null, ['label' => 'Utilisateur', 'required' => true]);
        $formMapper->add('status', null, ['label' => 'Actif', 'required' => true]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id');
        $datagridMapper->add('city');
        $datagridMapper->add('user');
        $datagridMapper->add('postalCode');
        $datagridMapper->add('phone');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id', null, ['label' => 'ID'])
            ->addIdentifier('user', null, ['label' => 'Utilisateur'])
            ->add('city', null, ['label' => 'Ville'])
            ->add('postalCode', null, ['label' => 'Code postal'])
            ->add('phone', null, ['label' => 'Téléphone'])
            ->add('status', null, ['label' => 'Actif']);
    }
}