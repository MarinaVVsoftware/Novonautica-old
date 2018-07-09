<?php

namespace AppBundle\Form\Contabilidad\Egreso;

use AppBundle\Entity\Contabilidad\Egreso\Entrada;
use AppBundle\Entity\Contabilidad\Egreso\Entrada\Concepto;
use AppBundle\Entity\Contabilidad\Egreso\Entrada\Proveedor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
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
            'importe',
            MoneyType::class,
            [
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'data' => 0,
                'attr' => ['class' => 'money-input'],
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

                $this->createConceptoField($form);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                $productoId = array_key_exists('concepto', $data) ? $data['concepto'] : null;
                $this->createConceptoField($form, $productoId);
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
        $proveedor = !$proveedorId
            ? []
            : [$this->entityManager->getRepository(Entrada::class)->find($proveedorId)];

        $form->add(
            'proveedor',
            EntityType::class,
            [
                'class' => Concepto::class,
                'choices' => $proveedor,
            ]
        );
    }
}
