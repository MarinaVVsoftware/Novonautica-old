<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/20/18
 * Time: 12:02 PM
 */

namespace AppBundle\Form\ModificacionInventario;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Range;

class Concepto extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'existencia',
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

        /*
         * Event listeners
         */

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                dump($event->getData());

                $this->createProductoField($event->getForm());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                dump($event->getData());

                $this->createProductoField($event->getForm());
            }
        );
    }

    private function createProductoField(FormInterface $form, $productoId = null)
    {
//        $producto = null === $productoId ? [] : FacturacionHelper::getProductoRepositoryByEmpresa($productoId);

        $form->add(
            'Producto',
            ChoiceType::class,
            [
                'choices' => [
                    'Un producto' => 1,
                    'Dos productos' => 2
                ]
            ]
        );
    }
}
