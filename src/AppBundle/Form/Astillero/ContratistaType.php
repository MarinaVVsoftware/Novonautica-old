<?php
/**
 * Created by PhpStorm.
 * User: Holograma
 * Date: 19/03/2018
 * Time: 10:37 PM
 */

namespace AppBundle\Form\Astillero;


use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ContratistaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cotizacionInicial',TextType::class,[
                'label' => 'Descripción del trabajo'
            ])
            ->add('precio',MoneyType::class,[
                'label' => 'Precio (USD)',
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('utilidadvv',MoneyType::class,[
                'label' => 'Utilidad V&V (USD)',
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('preciovv',MoneyType::class,[
                'label' => 'Precio V&V (USD)',
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('proveedor',EntityType::class,[
                'class' => 'AppBundle\Entity\Astillero\Proveedor',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class'=>'buscaproveedor'],

            ])
            ->add('pagos',MoneyType::class,[
                'label' => 'Pagos (USD)',
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('fecha',DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker-solo input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd',
                'data' => new DateTime()
            ])
            ->add('formaPago')
            ->add('saldo',MoneyType::class,[
                'label' => 'Saldo (USD)',
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal']
            ])
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Astillero\Contratista'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillerocontratista';
    }
}