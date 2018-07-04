<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom'
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Adresse'
                ]
            )
            ->add(
                'addressComplement',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Complément adresse'
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Ville',
                ]

            )
            ->add(
                'postalCode',
                IntegerType::class,
                [
                    'required' => true,
                    'label' => 'Code postal',
                ]

            )
            ->add(
                'phone',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Téléphone',
                ]

            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => true,
                    'label' => 'Description',
                ]

            )
            ->add(
                'seats',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'Places',
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