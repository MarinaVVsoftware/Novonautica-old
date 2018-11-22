<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 22/11/2018
 * Time: 03:43 PM
 */

namespace AppBundle\Form\Compra;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('validadoCompra',ChoiceType::class,[
                'label' => 'Validación',
                'choices' => [
                    'Pendiente' => null,
                    'Aceptar' => true,
                    'Rechazar' => false,
                ],
            ])
            ->add('notaCompra',TextareaType::class,[
                'required' => false,
                'attr' => ['rows' => 5, 'class' => 'info-input'],
                'label' => 'Notas'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Solicitud',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_solicitud';
    }
}