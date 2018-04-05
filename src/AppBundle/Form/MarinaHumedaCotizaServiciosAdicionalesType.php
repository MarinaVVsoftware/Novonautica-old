<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 17/11/2017
 * Time: 03:25 PM
 */

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MarinaHumedaCotizaServiciosAdicionalesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cantidad',TextType::class,[
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('marinahumedaservicio',EntityType::class,[
                'class' => 'AppBundle:MarinaHumedaServicio',
                'label' => ' ',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-busca-producto'],
                'required'=>false,

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