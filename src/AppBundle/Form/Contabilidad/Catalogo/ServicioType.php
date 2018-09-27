<?php

namespace AppBundle\Form\Contabilidad\Catalogo;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class ServicioType extends AbstractType
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
        $builder->add('codigo');
        $builder->add('nombre');

        $builder->add(
            'emisor',
            EntityType::class,
            [
                'label' => 'Empresa',
                'class' => Emisor::class,
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $this->createClaveProdServField($event->getForm(), $event->getData()->getClaveProdServ());
                $this->createClaveUnidadField($event->getForm(), $event->getData()->getClaveUnidad());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                $cu = array_key_exists('claveUnidad', $data)
                    ? $this->entityManager->getRepository(ClaveUnidad::class)->find($data['claveUnidad'])
                    : null;

                $cps = array_key_exists('claveProdServ', $data)
                    ? $this->entityManager->getRepository(ClaveProdServ::class)->find($data['claveProdServ'])
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
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contabilidad\Catalogo\Servicio',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_catalogo_servicio';
    }

    private function createClaveProdServField(
        FormInterface $form,
        ClaveProdServ $claveProdServ = null
    ) {
        $claves = null === $claveProdServ ? [] : [$claveProdServ];

        $form->add(
            'claveProdServ',
            EntityType::class,
            [
                'label' => 'Clave producto servicio',
                'class' => ClaveProdServ::class,
                'choices' => $claves,
                'choice_label' => 'descripcion',
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona una clave']),
                ],
            ]
        );
    }

    private function createClaveUnidadField(
        FormInterface $form,
        ClaveUnidad $claveUnidad = null
    ) {
        $claves = null === $claveUnidad ? [] : [$claveUnidad];

        $form->add(
            'claveUnidad',
            EntityType::class,
            [
                'label' => 'Clave unidad',
                'class' => ClaveUnidad::class,
                'choices' => $claves,
                'choice_label' => 'nombre',
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona una clave']),
                ],
            ]
        );
    }
}
