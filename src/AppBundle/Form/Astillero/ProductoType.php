<?php

namespace AppBundle\Form\Astillero;

use AppBundle\Entity\Astillero\Producto;
use AppBundle\Entity\Astillero\Proveedor;
use AppBundle\Form\Astillero\Producto\ProveedorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ProductoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('identificador');
        $builder->add('nombre');

        $builder->add('precio', MoneyType::class, [
            'attr' => ['class' => 'esdecimal', 'autocomplete' => 'off'],
            'currency' => 'MXN',
            'divisor' => 100,
            'grouping' => true,
            'empty_data' => 0,
            'label' => 'Precio (MXN)',
        ]);

        $builder->add('unidad');

        $builder->add(
            'proveedores',
            CollectionType::class,
            [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'label' => false,
                    'class' => Proveedor::class
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Astillero\Producto',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillero_producto';
    }

    private function createClaveUnidadField(FormInterface $form, ClaveUnidad $claveUnidad = null)
    {
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

    private function createProdServField(FormInterface $form, ClaveProdServ $claveProdServ = null)
    {
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
