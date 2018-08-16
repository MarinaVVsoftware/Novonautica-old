<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 09/08/2018
 * Time: 11:55 AM
 */

namespace AppBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CombustibleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dolar',MoneyType::class,[
                'required'=>false,
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off'],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0
            ])
            ->add('iva',TextType::class,[
                'attr' => ['class' => 'esdecimal calcular-costos','autocomplete' => 'off'],
                'label' => '% IVA'
            ])
            ->add('cuotaIesps', TextType::class,[
                'attr' => ['class' => 'esdecimal calcular-costos','autocomplete' => 'off'],
                'label' => 'Cuota IESPS'
            ])
            ->add('cantidad',TextType::class,[
                'attr' => ['class' => 'esdecimal calcular-costos','autocomplete' => 'off'],
                'label' => 'Litros'
            ])
            ->add('precioVenta',MoneyType::class,[
                'attr' => ['class' => 'esdecimal calcular-costos','autocomplete' => 'off'],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,

                'grouping' => true
            ])
            ->add('precioSinIesps',MoneyType::class,[
                'attr' => ['readonly' => true],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,
                'label_attr' => ['class' => 'letra-azul tipo-letra2'],
                'label' => 'Precio sin IESPS:',
                'grouping' => true
            ])
            ->add('precioSinIvaIesps',MoneyType::class,[
                'attr' => ['readonly' => true],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,
                'label_attr' => ['class' => 'letra-azul tipo-letra2'],
                'label' => 'Precio sin IVA/IESPS:',
                'grouping' => true
            ])
            ->add('subtotal', MoneyType::class,[
                'attr' => ['readonly' => true],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,
                'label_attr' => ['class' => 'letra-azul tipo-letra2'],
                'label' => 'Subtotal:',
                'grouping' => true
            ])
            ->add('ivaTotal', MoneyType::class,[
                'attr' => ['readonly' => true],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,
                'label_attr' => ['class' => 'letra-azul tipo-letra2'],
                'label' => 'IVA:',
                'grouping' => true
            ])
            ->add('iespsTotal', MoneyType::class,[
                'attr' => ['readonly' => true],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,
                'label_attr' => ['class' => 'letra-azul tipo-letra2'],
                'label' => 'IESPS:',
                'grouping' => true
            ])
            ->add('totalSinIesps', MoneyType::class,[
                'attr' => ['readonly' => true],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,
                'label_attr' => ['class' => 'letra-azul tipo-letra2'],
                'label' => 'Total sin IESPS:',
                'grouping' => true
            ])
            ->add('totalSinIvaIesps', MoneyType::class,[
                'attr' => ['readonly' => true],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,
                'label_attr' => ['class' => 'letra-azul tipo-letra2'],
                'label' => 'Total sin IVA/IESPS:',
                'grouping' => true
            ])
            ->add('total', MoneyType::class,[
                'attr' => ['readonly' => true],
                'divisor' => 100,
                'currency' => 'MXN',
                'empty_data' => 0,
                'label_attr' => ['class' => 'letra-azul tipo-letra2'],
                'label' => 'Total:',
                'grouping' => true
            ])
            ->add('tipo',EntityType::class,[
                'class' => 'AppBundle\Entity\Combustible\Catalogo',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'selecttipocombustible']
            ])
            ->add('barco',EntityType::class,[
                'class' => 'AppBundle\Entity\Barco',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-buscador selectbarcobuscar']
            ])
            ->add('mensaje',TextareaType::class,[
                'label' => 'Mensaje en el correo:',
                'attr' => ['rows' => 7, 'class' => 'editorwy'],
                'required' => false
            ])
            ->add('notificarCliente', CheckboxType::class, [
                'label' => '¿Notificar al cliente?',
                'required' => false
            ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $cotizacion = $event->getData();
            $form = $event->getForm();
            if($cotizacion->getFolio()){ // si es una cotizacion rechazada
                $form->remove('barco');
            }
        });


    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Combustible'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_combustible';
    }
}