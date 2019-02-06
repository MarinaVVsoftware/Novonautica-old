<?php

namespace AppBundle\Form\Marina\Tarifa;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use AppBundle\Entity\Marina\Tarifa\Tipo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class TipoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'tipo',
            ChoiceType::class,
            [
                'label' => 'Nombre',
                'choices' => Tipo::$tipos,
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $producto = $event->getData();

                $this->createClaveUnidadField($event->getForm(), $producto->getClaveUnidad());
                $this->createProdServField($event->getForm(), $producto->getClaveProdServ());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                $claveUnidad = array_key_exists('claveUnidad', $data)
                    ? $this->entityManager->getRepository(ClaveUnidad::class)->find($data['claveUnidad'])
                    : null;

                $claveProdServ = array_key_exists('claveProdServ', $data)
                    ? $this->entityManager->getRepository(ClaveProdServ::class)->find($data['claveProdServ'])
                    : null;

                $this->createClaveUnidadField($event->getForm(), $claveUnidad);
                $this->createProdServField($event->getForm(), $claveProdServ);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tipo::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marina_tarifa_tipo';
    }

    private function createClaveUnidadField(FormInterface $form, ClaveUnidad $claveUnidad = null)
    {
        $clavesUnidades = null === $claveUnidad ? [] : [$claveUnidad];

        $form->add(
            'claveUnidad',
            EntityType::class,
            [
                'class' => ClaveUnidad::class,
                'choice_label' => 'nombre',
                'choices' => $clavesUnidades,
                'required' => true,
                'attr' => [
                    'class' => 'sat-cu',
                    'required' => 'required'
                ],
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un valor']),
                    new NotBlank(['message' => 'Por favor selecciona un valor']),
                ],
            ]
        );
    }

    private function createProdServField(FormInterface $form, ClaveProdServ $claveProdServ = null)
    {
        $claveProdServs = null === $claveProdServ ? [] : [$claveProdServ];

        $form->add(
            'claveProdServ',
            EntityType::class,
            [
                'class' => ClaveProdServ::class,
                'choice_label' => 'descripcion',
                'choices' => $claveProdServs,
                'required' => true,
                'attr' => [
                    'class' => 'sat-cps',
                    'required' => 'required'
                ],
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un valor']),
                    new NotBlank(['message' => 'Por favor selecciona un valor']),
                ],
            ]
        );
    }
}
