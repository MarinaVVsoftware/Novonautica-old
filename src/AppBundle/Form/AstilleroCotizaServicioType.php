<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 27/10/2017
 * Time: 10:52 AM
 */

namespace AppBundle\Form;


use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\AstilleroCotizaServicio;
use AppBundle\Entity\Producto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AstilleroCotizaServicioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('servicio')
            ->add('cantidad')
            ->add('servicio')
            ->add('precio')
            ->add('estatus', null,[
                'label' => ' '
            ])
            ->add('producto',EntityType::class,[
                'class' => 'AppBundle:Producto',
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
            'data_class' => 'AppBundle\Entity\AstilleroCotizaServicio'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillerocotizaservicio';
    }
}