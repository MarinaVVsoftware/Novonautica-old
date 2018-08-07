<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 09/10/2017
 * Time: 04:35 PM
 */

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\MarinaHumedaTarifa;

class MarinaHumedaCotizaServiciosGasolinaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('servicio')
            ->add('cantidad',TextType::class,[
                'label' => 'Litros',
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off'],
                'required' => false
            ])
            ->add('precio', MoneyType::class,[
                'label' => 'Precio por litro sin iva',
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off', 'readonly' => true],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('precioAux', MoneyType::class,[
                'label' => 'Precio por litro',
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off'],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('subtotal', MoneyType::class,[
                'label' => 'Subtotal',
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off', 'readonly' => true],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('iva', MoneyType::class,[
                'label' => 'I.V.A.',
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off', 'readonly' => true],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('total', MoneyType::class,[
                'label' => 'Total',
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off', 'readonly' => true],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('combustible',EntityType::class,[
                'class' => 'AppBundle\Entity\Combustible\Catalogo',
                'placeholder' => 'Seleccionar...'
            ])
           ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MarinaHumedaCotizaServicios'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedacotizaservicios';
    }
}