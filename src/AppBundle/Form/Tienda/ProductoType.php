<?php

namespace AppBundle\Form\Tienda;

use AppBundle\Form\DataTransformer\ClaveProdServTransformer;
use AppBundle\Form\DataTransformer\ClaveUnidadTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Producto',
            ])
            ->add('precio', MoneyType::class, [
                'divisor' => 100,
                'currency' => 'MXN',
                'label' => 'Precio Público'
            ])
            ->add('preciocolaborador', MoneyType::class, [
                'divisor' => 100,
                'currency' => 'MXN',
                'label' => 'Precio Colaborador'
            ])
            ->add('codigoBarras', TextType::class, [
                'label' => 'Código de Barras',
            ])
            ->add('imagenFile', VichImageType::class, [
                'label' => 'Imagen',
                'allow_delete' => false,
                'required' => false,
            ])
            ->add('claveUnidad', TextType::class)
            ->add('claveProdServ', TextType::class, [
                'label' => 'Clave Producto'
            ]);

        $builder->get('claveProdServ')
            ->addModelTransformer($this->cpsTransformer);
        $builder->get('claveUnidad')
            ->addModelTransformer($this->cuTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tienda\Producto'
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
