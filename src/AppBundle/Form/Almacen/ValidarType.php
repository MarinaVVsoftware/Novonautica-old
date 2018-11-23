<?php

namespace AppBundle\Form\Almacen;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('referencia',TextType::class,[
                'required' => false
            ])
            ->add('validadoAlmacen',CheckboxType::class,[
                'label' => 'Validar',
                'required' => false,
            ])
            ->add('notaAlmacen',TextareaType::class,[
                'required' => false,
                'attr' => ['rows' => 5, 'class' => 'info-input'],
                'label' => 'Notas'
            ])
            ->add('conceptos',CollectionType::class,[
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => false,
                'by_reference' => false,
                'allow_delete' => false,
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