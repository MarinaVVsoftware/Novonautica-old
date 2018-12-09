<?php

namespace AppBundle\Form\JRFMarine\Categoria;

use AppBundle\Entity\JRFMarine\Categoria;
use AppBundle\Entity\JRFMarine\Categoria\Subcategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SubcategoriaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre');

        $builder->add(
            'imagenFile',
            VichImageType::class,
            [
                'label' => 'Imagen',
                'allow_delete' => false,
                'required' => false,
            ]
        );

        $builder->add(
            'categoria',
            EntityType::class,
            [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una categoria',
                'constraints' => [
                    new NotNull(['message' => 'Por favor seleccione una categoria.']),
                    new NotBlank(['message' => 'Por favor seleccione una categoria.']),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subcategoria::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_jrfmarine_categoria_subcategoria';
    }


}
