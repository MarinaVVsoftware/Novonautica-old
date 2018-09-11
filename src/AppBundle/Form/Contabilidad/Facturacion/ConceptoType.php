<?php

namespace AppBundle\Form\Contabilidad\Facturacion;

use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Form\DataTransformer\ClaveProdServTransformer;
use AppBundle\Form\DataTransformer\ClaveUnidadTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;

class ConceptoType extends AbstractType
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
        $moneyOptions = [
            'label' => false,
            'attr' => [
                'class' => 'money-input',
            ],
            'currency' => 'MXN',
            'divisor' => 100,
            'grouping' => true,
        ];

        $builder->add(
            'cantidad',
            IntegerType::class,
            [
                'label' => false,
                'attr' => ['min' => 1, 'step' => 'any'],
                'scale' => 2,
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'minMessage' => 'El valor minimo es 1',
                    ]),
                ],
            ]
        );

        $builder->add(
            'unidad',
            TextType::class,
            [
                'label' => false,
            ]
        );

        $builder->add(
            'descripcion',
            TextType::class,
            [
                'label' => false,
            ]
        );

        $builder->add(
            'impuesto',
            ChoiceType::class,
            [
                'label' => false,
                'choices' => Facturacion::$impuestos,
            ]
        );

        $builder->add(
            'tipoFactor',
            ChoiceType::class,
            [
                'label' => false,
                'choices' => Facturacion::$factores,
            ]
        );

        $builder->add(
            'tasaOCuota',
            TextType::class,
            [
                'label' => false,
            ]
        );

        $builder->add('valorunitario', MoneyType::class, $moneyOptions);
        $builder->add('importe', MoneyType::class, $moneyOptions);
        $builder->add('base', MoneyType::class, $moneyOptions);
        $builder->add('impuestoImporte', MoneyType::class, $moneyOptions);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $this->createClaveProdServField($event->getForm());
                $this->createClaveUnidadField($event->getForm());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                $cu = array_key_exists('claveUnidad', $data)
                    ? $this->entityManager->getRepository(Facturacion\Concepto\ClaveUnidad::class)->find($data['claveUnidad'])
                    : null;

                $cps = array_key_exists('claveProdServ', $data)
                    ? $this->entityManager->getRepository(Facturacion\Concepto\ClaveProdServ::class)->find($data['claveProdServ'])
                    : null;

                $this->createClaveUnidadField($event->getForm(), $cu);
                $this->createClaveProdServField($event->getForm(), $cps);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facturacion\Concepto::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_facturacion_concepto';
    }

    private function createClaveProdServField(
        FormInterface $form,
        Facturacion\Concepto\ClaveProdServ $claveProdServ = null
    ) {
        $claves = null === $claveProdServ ? [] : [$claveProdServ];

        $form->add(
            'claveProdServ',
            EntityType::class,
            [
                'label' => false,
                'class' => Facturacion\Concepto\ClaveProdServ::class,
                'choices' => $claves,
                'choice_label' => 'descripcion',
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona una clave'])
                ]
            ]
        );
    }

    private function createClaveUnidadField(
        FormInterface $form,
        Facturacion\Concepto\ClaveUnidad $claveUnidad = null
    ) {
        $claves = null === $claveUnidad ? [] : [$claveUnidad];

        $form->add(
            'claveUnidad',
            EntityType::class,
            [
                'label' => false,
                'class' => Facturacion\Concepto\ClaveUnidad::class,
                'choices' => $claves,
                'choice_label' => 'nombre',
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona una clave'])
                ]
            ]
        );
    }
}
