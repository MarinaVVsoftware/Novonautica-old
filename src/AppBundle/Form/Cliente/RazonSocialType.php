<?php

namespace AppBundle\Form\Cliente;

use AppBundle\Entity\Contabilidad\Facturacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class RazonSocialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rfc', TextType::class, ['label' => 'RFC']);

        $builder->add(
            'usoCFDI',
            ChoiceType::class,
            [
                'label' => 'Uso CFDI',
                'choices' => Facturacion::$CFDIS,
            ]

        );

        $builder->add('razonSocial', TextType::class, ['label' => 'Razón Social']);
        $builder->add('direccion', TextType::class, ['label' => 'Dirección']);
        $builder->add(
            'correos',
            EmailType::class,
            [
                'label' => 'Correos de recepción (separados por comas)',
                'attr' => ['multiple' => 'multiple'],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},?)+$/',
                        'message' => 'No estas ingresando correos validos, separados por comas.'
                    ]),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cliente\RazonSocial',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cliente_razonsocial';
    }


}
