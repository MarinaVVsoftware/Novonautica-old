<?php

namespace AppBundle\Form\Contabilidad;

use AppBundle\Entity\Contabilidad\Egreso\Tipo;
use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use AppBundle\Form\Contabilidad\Egreso\EntradaType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EgresoType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'fecha',
            DateType::class,
            [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker-solo input-calendario',
                    'readonly' => true,
                ],
                'format' => 'yyyy-MM-dd',
            ]
        );

        $builder->add(
            'empresa',
            EntityType::class,
            [
                'class' => Emisor::class,
                'query_builder' => function (EntityRepository $er) {
                    $query = $er->createQueryBuilder('e');
                    $views = [];

                    foreach ($this->security->getUser()->getRoles() as $role) {
                        if (strpos($role, 'ROLE_ADMIN') === 0) {
                            return $query;
                        }

                        if (strpos($role, 'VIEW_EGRESO') === 0) {
                            $views[] = explode('_', $role)[3];
                        }
                    }

                    return $query->where(
                        $query->expr()->in('e.id', $views)
                    );
                },
                'required' => true,
            ]
        );

        $builder->add(
            'tipo',
            EntityType::class,
            [
                'class' => Tipo::class,
                'choice_label' => 'descripcion',
                'required' => true,
            ]
        );

        $builder->add(
            'entradas',
            CollectionType::class,
            [
                'label' => false,
                'entry_type' => EntradaType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]
        );
        $builder->add(
            'iva',
            TextType::class,
            [
                'label' => '% I.V.A.',
                'attr' => ['class' => 'esdecimal'],
            ]
        );
        $builder->add(
            'subtotal',
            MoneyType::class,
            [
                'label' => false,
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'money-input'],
            ]
        );
        $builder->add(
            'ivatotal',
            MoneyType::class,
            [
                'label' => false,
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'money-input'],
            ]
        );
        $builder->add(
            'total',
            MoneyType::class,
            [
                'label' => false,
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'money-input'],
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $egreso = $event->getData();

                if ($egreso->getId()) {
                    $form->add(
                        'comentarioEditar',
                        TextType::class,
                        [
                            'label' => 'Motivo de edición: ',
                            'required' => true,
                            'constraints' => [
                                new NotBlank(),
                                new Length([
                                    'min' => 5,
                                    'minMessage' => 'El comentario del motivo debe ser mayor a {{ limit }} caracteres',
                                ]),
                            ],
                        ]
                    );
                }
            });

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contabilidad\Egreso',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_egreso';
    }


}
