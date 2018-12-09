<?php

namespace AppBundle\Form\JRFMarine;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use AppBundle\Entity\JRFMarine\Categoria;
use AppBundle\Entity\JRFMarine\Subcategoria;
use AppBundle\Entity\JRFMarine\Marca;
use AppBundle\Entity\JRFMarine\Producto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre');
        $builder->add('unidad');
        $builder->add('existencia');

        $builder->add(
            'precio',
            MoneyType::class,
            [
                'divisor' => 100,
                'currency' => 'MXN',
            ]
        );

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
            'marca',
            EntityType::class,
            [
                'class' => Marca::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una marca',
                'constraints' => [
                    new NotNull(['message' => 'Por favor seleccione una marca.']),
                    new NotBlank(['message' => 'Por favor seleccione una marca.']),
                ],
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

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Producto $producto */
                $producto = $event->getData();

                $this->createClaveUnidadField($event->getForm(), $producto->getClaveUnidad());
                $this->createProdServField($event->getForm(), $producto->getClaveProdServ());
                $this->createSubcategoriaField($event->getForm(), $producto->getCategoria());
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

        $builder->get('categoria')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $categoria = $event->getForm()->getData();
                $this->createSubcategoriaField($event->getForm()->getParent(), $categoria);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_jrfmarine_producto';
    }

    private function createClaveUnidadField(FormInterface $form, ClaveUnidad $claveUnidad = null) {
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

    private function createProdServField(FormInterface $form, ClaveProdServ $claveProdServ = null) {
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

    private function createSubcategoriaField(FormInterface $form, Categoria $categoria = null)
    {
        $subcategorias = !$categoria
            ? []
            : $this->entityManager
                ->getRepository(Categoria\Subcategoria::class)
                ->findBy(['categoria' => $categoria]);

        $form->add(
            'subcategoria',
            EntityType::class,
            [
                'class' => Categoria\Subcategoria::class,
                'choice_label' => 'nombre',
                'placeholder' => '',
                'choices' => $subcategorias,
                'constraints' => [
                    new NotNull(['message' => 'Por favor seleccione una subcategoria.']),
                    new NotBlank(['message' => 'Por favor seleccione una subcategoria.']),
                ],
            ]
        );
    }
}
