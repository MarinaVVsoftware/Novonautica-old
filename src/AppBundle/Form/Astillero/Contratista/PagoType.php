<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 09/04/2018
 * Time: 04:24 PM
 */

namespace AppBundle\Form\Astillero\Contratista;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cantidad', MoneyType::class, [
                'required'=>false,
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off'],
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('divisa',ChoiceType::class,[
                'choices'  => ['MXN' => 'MXN','USD' => 'USD'],

            ])
            ->add('fecha',DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd'
            ])
            ->add('forma',TextType::class,[
                'label' => 'Forma de pago',

            ])
        ;
    }

    /**
    * {@inheritdoc}
    */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Astillero\Contratista\Pago'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillero_contratista_pago';
    }

}