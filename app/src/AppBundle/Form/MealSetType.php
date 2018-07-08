<?php
namespace AppBundle\Form;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MealSetType extends AbstractType
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
                    'required' => false,
                    'label' => 'Description'
                ]
            )
            ->add(
                'price',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'Prix'
                ]
            )
            ->add(
                'position',
                IntegerType::class,
                [
                    'label' => 'Position'
                ]
            );
    }
}