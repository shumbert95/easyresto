<?php
namespace AppBundle\Form;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Prénom'
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Nom'
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Email'
                ]
            )
            ->add(
                'type',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'Type',
                ]

            )
            ->add(
                'civility',
                IntegerType::class,
                [
                    'required' => true,
                    'label' => 'Civilité',
                ]

            )
            ->add(
                'birthDate',
                DateType::class,
                [
                    'required' => false,
                    'label' => 'Année de naissance',
                    'widget' => 'single_text',
                ]

            )
            ->add(
                'phoneNumber',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Numéro de téléphone',
                ]

            )
            ->add(
                'postalCode',
                IntegerType::class,
                [
                    'required' => true,
                    'label' => 'Code Postal',
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