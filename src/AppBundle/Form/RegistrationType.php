<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                null,
                [
                    'required' => true,
                    'label' => 'Prénom'
                ]
            )
            ->add(
                'lastName',
                null,
                [
                    'required' => true,
                    'label' => 'Nom'
                ]
            )
            ->add(
                'type',
                null,
                [
                    'required' => true,
                    'label' => 'Type',
                ]

            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Valider'
                ]
            );
    }
}