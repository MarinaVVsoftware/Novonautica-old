<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 06/07/2018
 * Time: 05:01 PM
 */

namespace AppBundle\Form\Astillero\Contratista;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubActividadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,[
                'label' => 'Título'
            ])
//            ->add('inicio', DateType::class, [
//                'widget' => 'single_text',
//                'label' => 'Inicio pausa',
//                'html5' => false,
//                'attr' => [
//                    'class' => 'input-calendario',
//                    'readonly' => true
//                ],
//                'format' => 'yyyy-MM-dd',
//                'empty_data' => new \DateTime()
//            ])

            ->add('notas', TextareaType::class, [
                'label' => 'Motivo de la pausa',
                'required' => false,
                'attr' => ['rows' => 3]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Astillero\Contratista\Actividad'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillero_contratista_actividad';
    }

}