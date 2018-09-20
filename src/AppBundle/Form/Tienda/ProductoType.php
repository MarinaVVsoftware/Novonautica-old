<?php

namespace AppBundle\Form\Tienda;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use AppBundle\Entity\Tienda\Producto;
use AppBundle\Entity\Tienda\Producto\Categoria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public
    function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
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
                    'class' => 'percent-input',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Este campo no puede estar vacio']),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'El valor minimo es 100',
                        'maxMessage' => 'El valor maximo es 100',
                    ]),
                ],
            ]
        );

        $builder->add(
            'iVA',
            PercentType::class,
            [
                'label' => 'IVA',
                'type' => 'integer',
                'attr' => [
                    'class' => 'percent-input',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Este campo no puede estar vacio']),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'El valor minimo es 100',
                        'maxMessage' => 'El valor maximo es 100',
                    ]),
                ],
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Producto $producto */
                $producto = $event->getData();

                $this->createClaveUnidadField($event->getForm(), $producto->getClaveUnidad());
                $this->createProdServField($event->getForm(), $producto->getClaveProdServ());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                $claveUnidad = array_key_exists('claveUnidad', $data)
                    ? $this->entityManager->getRepository(ClaveUnidad::class)->find($data['claveUnidad'])
                    : null;

                $claveProdServ = array_key_exists('claveProdServ', $data)
                    ? $this->entityManager->getRepository(ClaveProdServ::class)->find($data['claveProdServ'])
                    : null;

                $this->createClaveUnidadField($event->getForm(), $claveUnidad);
                $this->createProdServField($event->getForm(), $claveProdServ);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public
    function configureOptions(
        OptionsResolver $resolver
    ) {
        $resolver->setDefaults([
            'data_class' => Producto::class,

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_producto';
    }

    private function createClaveUnidadField(
        FormInterface $form,
        ClaveUnidad $claveUnidad = null
    ) {
        $clavesUnidades = null === $claveUnidad ? [] : [$claveUnidad];

        $form->add(
            'claveUnidad',
            EntityType::class,
            [
                'class' => ClaveUnidad::class,
                'choice_label' => 'nombre',
                'choices' => $clavesUnidades,
                'required' => true,
                'attr' => ['required' => 'required'],
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un valor']),
                    new NotBlank(['message' => 'Por favor selecciona un valor']),
                ],
            ]
        );
    }

    private function createProdServField(
        FormInterface $form,
        ClaveProdServ $claveProdServ = null
    ) {
        $claveProdServs = null === $claveProdServ ? [] : [$claveProdServ];

        $form->add(
            'claveProdServ',
            EntityType::class,
            [
                'class' => ClaveProdServ::class,
                'choice_label' => 'descripcion',
                'choices' => $claveProdServs,
                'required' => true,
                'attr' => ['required' => 'required'],
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un valor']),
                    new NotBlank(['message' => 'Por favor selecciona un valor']),
                ],
            ]
        );
    }
}
