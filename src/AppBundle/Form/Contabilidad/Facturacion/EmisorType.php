<?php

namespace AppBundle\Form\Contabilidad\Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EmisorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alias')
            ->add('rfc', TextType::class, [
                'label' => 'RFC',
                'attr' => ['placeholder' => 'AAA010101AAA']
            ])
            ->add('regimenFiscal', ChoiceType::class, [
                'choices' => [
                    'General de Ley Personas Morales' => '601',
                    'Personas Morales con Fines no Lucrativos' => '603',
                    'Sueldos y Salarios e Ingresos Asimilados a Salarios' => '605',
                    'Arrendamiento' => '606',
                    'Demás ingresos' => '608',
                    'Consolidación' => '609',
                    'Residentes en el Extranjero sin Establecimiento Permanente en México' => '610',
                    'Ingresos por Dividendos (socios y accionistas)' => '611',
                    'Personas Físicas con Actividades Empresariales y Profesionales' => '612',
                    'Ingresos por intereses' => '614',
                    'Sin obligaciones fiscales' => '616',
                    'Sociedades Cooperativas de Producción que optan por diferir sus ingresos' => '620',
                    'Incorporación Fiscal' => '621',
                    'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras' => '622',
                    'Opcional para Grupos de Sociedades' => '623',
                    'Coordinados' => '624',
                    'Hidrocarburos' => '628',
                    'Régimen de Enajenación o Adquisición de Bienes' => '607',
                    'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales' => '629',
                    'Enajenación de acciones en bolsa de valores' => '630',
                    'Régimen de los ingresos por obtención de premios' => '615',
                ]
            ])
            ->add('nombre', TextType::class, [
                'label' => 'Razón social',
                'attr' => ['placeholder' => 'SERVICIOS EMPRESARIALES SC']
            ])
            ->add('logoFile', VichImageType::class, [
                'label' => 'Logo',
                'download_label' => 'Ver Logo',
                'allow_delete' => false,
                'required' => false
            ])
            ->add('cerFile', VichFileType::class, [
                'label' => 'Archivo CER',
                'allow_delete' => false,
                'download_label' => '.cer',
                'required' => false
            ])
            ->add('keyFile', VichFileType::class, [
                'label' => 'Archivo KEY',
                'allow_delete' => false,
                'download_label' => '.key',
                'required' => false
            ])
            ->add('usuarioPAC', TextType::class, ['label' => 'Usuario PAC'])
            ->add('passwordPAC', TextType::class, ['label' => 'Password PAC'])
            ->add('emails', TextType::class, [
                'label' => 'Emails de recepción (Separados por comas)',
                'attr' => ['placeholder' => 'john@marina.com, jperez@novonautica.com']
            ])
            ->add('password')
            ->add('codigoPostal')
            ->add('direccion')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contabilidad\Facturacion\Emisor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_facturacion_emisor';
    }


}
