<?php
namespace AppBundle\Form;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MealType extends AbstractType
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
                'description',
                TextType::class,
                [
                    'label' => 'Description'
                ]
            )
            ->add(
                'price',
                IntegerType::class,
                [
                    'label' => 'Prix'
                ]
            )
            ->add(
                'availability',
                BooleanType::class,
                [
                    'label' => 'Disponibilité'
                ]
            )
            ->add(
                'initialStock',
                IntegerType::class,
                [
                    'label' => 'Stock initial'
                ]
            )
            ->add(
                'currentStock',
                IntegerType::class,
                [
                    'label' => 'Stock actuel'
                ]
            )
            ->add(
                'categories',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => 'Catégories'
                ]
            );
    }
}