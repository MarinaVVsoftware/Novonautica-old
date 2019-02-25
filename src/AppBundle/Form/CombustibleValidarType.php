<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 14/08/2018
 * Time: 01:02 PM
 */

namespace AppBundle\Form;


use AppBundle\Entity\Combustible;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CombustibleValidarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('validanovo', ChoiceType::class, [
                'choices' => ['Aceptar' => 2, 'Rechazar' => 1],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => function ($val, $key, $index) {
                    return ['class' => 'opcion' . strtolower($key)];
                },
            ])
            ->add('notasnovo', TextareaType::class, [
                'label' => 'Observaciones',
                'attr' => ['rows' => 7],
                'required' => false
            ])
            ->add('validacliente', ChoiceType::class, [
                'choices' => ['Aceptar' => 2, 'Rechazar' => 1],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => function ($val, $key, $index) {
                    return ['class' => 'opcion' . strtolower($key)];
                },
            ])
            ->add('notascliente', TextareaType::class, [
                'label' => 'Observaciones',
                'attr' => ['rows' => 7],
                'required' => false
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $cotizacion = $event->getData();
            $form = $event->getForm();

            if($cotizacion->getValidanovo() === 2){ //aceptar como cliente
                $form
                    ->remove('validanovo')
                    ->remove('notasnovo');
            }else{ //validar como alguien de Novo
                $form
                    ->remove('validacliente')
                    ->remove('notascliente');
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Combustible::class,
            'constraints' => [
                new Callback([$this, 'combustibleHaveStock'])
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_combustible';
    }

    public function combustibleHaveStock(Combustible $cotizacion, ExecutionContextInterface $context)
    {
        $existencia = $cotizacion->getTipo()->getExistencia();

        if ($cotizacion->getValidanovo() === 2 && $existencia <= 0) {
            $context
                ->buildViolation('No hay suficiente inventario, Existencia actual: '.$existencia)
                ->atPath('validanovo')
                ->addViolation();
        }
    }
}
