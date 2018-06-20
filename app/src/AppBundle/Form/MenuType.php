<?php
namespace AppBundle\Form;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MenuType extends AbstractType
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
                TextType::class,
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
                'meals',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => 'Plats'
                ]
            );
    }
}