<?php

namespace AppBundle\Form\Contabilidad\Egreso;

use AppBundle\Entity\Contabilidad\Egreso\Entrada;
use AppBundle\Entity\Contabilidad\Egreso\Entrada\Concepto;
use AppBundle\Entity\Contabilidad\Egreso\Entrada\Proveedor;
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

class EntradaType extends AbstractType
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
        $builder->add(
            'subtotal',
            MoneyType::class,
            [
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,

                'attr' => ['class' => 'money-input subtotal','readonly' => 'readonly'],
            ]
        );
        $builder->add(
            'ivatotal',
            MoneyType::class,
            [
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,

                'attr' => ['class' => 'money-input ivatotal','readonly' => 'readonly'],
            ]
        );
        $builder->add(
            'importe',
            MoneyType::class,
            [
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,

                'attr' => ['class' => 'money-input importe'],
            ]
        );

        $builder->add(
            'comentario',
            TextType::class,
            [
                'required' => false
            ]
        );

        $builder->add(
            'proveedor',
            EntityType::class,
            [
                'class' => Proveedor::class,
                'choice_label' => 'nombre',
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $entrada = $event->getData();

                $this->createConceptoField($form, $entrada ? $entrada->getConcepto()->getId() : null);
                $this->createProveedorField($form, $entrada ? $entrada->getProveedor()->getId() : null);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                $conceptoId = array_key_exists('concepto', $data) ? $data['concepto'] : null;
                $proveedorId = array_key_exists('proveedor', $data) ? $data['proveedor'] : null;
                $this->createConceptoField($form, $conceptoId);
                $this->createProveedorField($form, $proveedorId);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contabilidad\Egreso\Entrada',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_egreso_entrada';
    }

    private function createConceptoField(FormInterface $form, $conceptoId = null)
    {
        $concepto = !$conceptoId
            ? []
            : [$this->entityManager->getRepository(Concepto::class)->find($conceptoId)];

        $form->add(
            'concepto',
            EntityType::class,
            [
                'class' => Concepto::class,
                'choices' => $concepto,
                'choice_label' => 'descripcion'
            ]
        );
    }

    private function createProveedorField(FormInterface $form, $proveedorId = null)
    {
        $proveedores = !$proveedorId
            ? []
            : [$this->entityManager->getRepository(Proveedor::class)->find($proveedorId)];

        $form->add(
            'proveedor',
            EntityType::class,
            [
                'class' => Proveedor::class,
                'choices' => $proveedores,
                'choice_label' => 'nombre'
            ]
        );
    }
}
