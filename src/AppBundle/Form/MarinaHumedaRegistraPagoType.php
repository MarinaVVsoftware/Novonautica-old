<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 27/11/2017
 * Time: 04:25 PM
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MarinaHumedaRegistraPagoType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estatuspago', ChoiceType::class,[
                'choices' =>[ 'Pagado' => 1, 'No Pagado' => 0 ],
                'expanded' => true,
                'multiple' => false,
                'label' => ' ',

            ])
            ->add('fecharealpago',DateType::class,[
                'label' => 'Fecha real del pago',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker-solo input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd'
            ])
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MarinaHumedaCotizacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedacotizacion';
    }
}