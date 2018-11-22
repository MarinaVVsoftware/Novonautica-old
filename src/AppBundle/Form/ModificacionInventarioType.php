<?php

namespace AppBundle\Form;

use AppBundle\Entity\ModificacionInventario;
use AppBundle\Form\ModificacionInventario\ConceptoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModificacionInventarioType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $modificacion = $options['data'];

        $builder->add(
            'conceptos',
            CollectionType::class,
            [
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => [
                    'label' => false,
                    'empresa' => $modificacion->getEmpresa()->getId(),
                ],
                'allow_add' => true,
                'constraints' => [
                    new Count(
                        [
                            'min' => 1,
                            'minMessage' => 'Debes agregar al menos un producto'
                        ]
                    )
                ]
            ]
        );

        $builder->add(
            'comentario',
            TextType::class,
            [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor explica la razon de la modificación del inventario'
                    ])
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModificacionInventario::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_modificacioninventario';
    }
}
