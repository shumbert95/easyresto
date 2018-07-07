<?php
namespace AppBundle\Form;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class IngredientType extends AbstractType
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
                'stock',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'Stock'
                ]
            );
    }
}