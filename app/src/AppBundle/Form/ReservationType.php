<?php
namespace AppBundle\Form;
use Sonata\AdminBundle\Form\Type\Filter\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nbParticipants',
                IntegerType::class,
                [
                    'required' => true
                ]
            )->add(
                'total',
                MoneyType::class,
                [
                    'required' => true
                ]
            )->add(
                'date',
                DateTimeType::class,
                [
                    'required' => true,
                    'widget' => 'single_text',
                    'date_format' => 'yyyy-MM-dd HH:mm'
                ]
            );
    }
}