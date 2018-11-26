<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/20/18
 * Time: 12:02 PM
 */

namespace AppBundle\Form\ModificacionInventario;

use AppBundle\Extra\FacturacionHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'antiguaExistencia',
            IntegerType::class,
            [
                'label' => false,
                'attr' => ['readonly' => 'readonly'],
                'scale' => 2,
            ]
        );

        $builder->add(
            'existencia',
            IntegerType::class,
            [
                'label' => false,
                'attr' => ['min' => 0, 'step' => 'any'],
                'scale' => 2,
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'minMessage' => 'El valor minimo es 0',
                    ]),
                ],
            ]
        );

        /*
         * Event listeners
         */

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($options) {
                $this->createProductoField(
                    $event->getForm(),
                    $options['empresa'],
                    $event->getData()['producto']
                );
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                $this->createProductoField(
                    $event->getForm(),
                    $options['empresa'],
                    $event->getData()['producto']
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_modificacioninventario_concepto';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['empresa']);
    }

    private function createProductoField(FormInterface $form, $emisorId, $productoId = null)
    {
        $productoRepository = FacturacionHelper::getProductoRepositoryByEmpresa($this->entityManager, $emisorId);

        if ($productoId) {
            $producto = $productoRepository->find($productoId);
            $productos = [$producto->getNombre() => $producto->getId()];
        } else {
            $productos = [];
        }

        $form->add(
            'producto',
            ChoiceType::class,
            [
                'choices' => $productos,
            ]
        );
    }
}
