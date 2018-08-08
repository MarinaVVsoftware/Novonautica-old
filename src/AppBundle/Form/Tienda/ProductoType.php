<?php

namespace AppBundle\Form\Tienda;

use AppBundle\Entity\Tienda\Producto\Categoria;
use AppBundle\Form\DataTransformer\ClaveProdServTransformer;
use AppBundle\Form\DataTransformer\ClaveUnidadTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductoType extends AbstractType
{
    private $cpsTransformer;
    private $cuTransformer;

    public function __construct(ClaveProdServTransformer $cpsTransformer, ClaveUnidadTransformer $cuTransformer)
    {
        $this->cpsTransformer = $cpsTransformer;
        $this->cuTransformer = $cuTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'nombre',
            TextType::class,
            [
                'label' => 'Producto',
            ]
        );

        $builder->add(
            'precio',
            MoneyType::class,
            [
                'divisor' => 100,
                'currency' => 'MXN',
                'label' => 'Precio Público',
            ]
        );

        $builder->add(
            'preciocolaborador',
            MoneyType::class,
            [
                'divisor' => 100,
                'currency' => 'MXN',
                'label' => 'Precio Colaborador',

            ]);

        $builder->add(
            'codigoBarras',
            TextType::class,
            [
                'label' => 'Código de Barras',

            ]
        );

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
                'constraints' => [
                    new NotNull(['message' => 'Por favor seleccione una categoria.']),
                    new NotBlank(['message' => 'Por favor seleccione una categoria.']),
                ],
            ]
        );

        $builder->add(
            'iESPS',
            PercentType::class,
            [
                'label' => 'IESPS',
                'type' => 'integer',
                'attr' => [
                    'class' => 'percent-input'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Este campo no puede estar vacio']),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'El valor minimo es 100',
                        'maxMessage' => 'El valor maximo es 100',
                    ])
                ]
            ]
        );

        $builder->add(
            'iVA',
            PercentType::class,
            [
                'label' => 'IVA',
                'type' => 'integer',
                'attr' => [
                    'class' => 'percent-input'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Este campo no puede estar vacio']),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'El valor minimo es 100',
                        'maxMessage' => 'El valor maximo es 100',
                    ])
                ]
            ]
        );

        $builder->add(
            'claveUnidad',
            TextType::class
        );

        $builder->add(
            'claveProdServ',
            TextType::class,
            [
                'label' => 'Clave Producto',

            ]
        );

        $builder->get('claveProdServ')->addModelTransformer($this->cpsTransformer);
        $builder->get('claveUnidad')->addModelTransformer($this->cuTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tienda\Producto',

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_producto';
    }


}
