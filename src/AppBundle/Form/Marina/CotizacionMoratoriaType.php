<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 25/05/2018
 * Time: 04:41 PM
 */

namespace AppBundle\Form\Marina;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotizacionMoratoriaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('porcentajeMoratorio',TextType::class,[
                'label'=>'% Moratorio',
                'attr' => ['class' => 'esdecimal'],
            ])
//            ->add('moratoriaTotal',MoneyType::class,[
//                'attr' => ['class' => 'esdecimal','autocomplete' => 'off','readonly' => 'readonly'],
//                'currency' => 'USD',
//                'divisor' => 100
//            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\MarinaHumedaCotizacion'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedacotizacion';
    }
}