<?php
namespace AppBundle\Form;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                'region',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Région'
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
                'website',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Site Web',
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
                'latitude',
                NumberType::class,
                [
                    'required' => true,
                    'label' => 'Latitude',
                ]

            )
            ->add(
                'longitude',
                NumberType::class,
                [
                    'required' => true,
                    'label' => 'Longitude',
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