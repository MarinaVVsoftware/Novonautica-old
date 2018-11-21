<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 20/11/2018
 * Time: 01:18 PM
 */

namespace AppBundle\Form\Almacen;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('validadoAlmacen',CheckboxType::class,[
                'label' => false,
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Solicitud\Concepto',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_solicitud_concepto';
    }
}