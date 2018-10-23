<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 09/10/2018
 * Time: 03:55 PM
 */

namespace AppBundle\Form\Gasto;


use AppBundle\Entity\Contabilidad\Catalogo\Servicio;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConceptoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('servicio',EntityType::class,[
                'class' => 'AppBundle\Entity\Contabilidad\Catalogo\Servicio',
                'placeholder' => 'Seleccionar...',
                'choice_attr' => function($choiceValue, $key, $value) {
                    // adds a class like attending_yes, attending_no, etc
                    return ['class' => 'opcion_'.strtolower($key)];
                },
                'choice_value' => function (Servicio $entity = null) {
                    return $entity ? $entity->getId().','.$entity->getEmisor()->getId() : '';
                },
            ])
            ->add('total',MoneyType::class,[
                'attr' => ['class' => 'esdecimal servicioTotal'],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Gasto\Concepto',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_gasto_concepto';
    }
}