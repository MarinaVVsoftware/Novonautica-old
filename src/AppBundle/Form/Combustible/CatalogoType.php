<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 07/08/2018
 * Time: 05:11 PM
 */

namespace AppBundle\Form\Combustible;


use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class CatalogoType extends AbstractType
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
            'nombre',
            TextType::class,
            [
                'label' => 'Combustible',
            ]
        );

        $builder->add(
            'precio',
            MoneyType::class,
            [
                'attr' => ['class' => 'esdecimal'],
                'currency' => 'MXN',
                'divisor' => 100,
            ]
        );

        $builder->add(
            'cuotaIesps',
            TextType::class,
            [
                'label' => 'Cuota IESPS',
                'attr' => ['class' => 'esdecimal'],
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
            'data_class' => 'AppBundle\Entity\Combustible\Catalogo',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_combustible_catalogo';
    }

    private function createClaveUnidadField(
        FormInterface $form,
        ClaveUnidad $claveUnidad = null
    ) {
        $clavesUnidades = null === $claveUnidad ? [] : [$claveUnidad];

        $form->add(
            'claveUnidad',
            EntityType::class,
            [
                'class' => ClaveUnidad::class,
                'choice_label' => 'nombre',
                'choices' => $clavesUnidades,
                'required' => true,
                'attr' => ['required' => 'required'],
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un valor']),
                    new NotBlank(['message' => 'Por favor selecciona un valor']),
                ],
            ]
        );
    }

    private function createProdServField(
        FormInterface $form,
        ClaveProdServ $claveProdServ = null
    ) {
        $claveProdServs = null === $claveProdServ ? [] : [$claveProdServ];

        $form->add(
            'claveProdServ',
            EntityType::class,
            [
                'class' => ClaveProdServ::class,
                'choice_label' => 'descripcion',
                'choices' => $claveProdServs,
                'required' => true,
                'attr' => ['required' => 'required'],
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un valor']),
                    new NotBlank(['message' => 'Por favor selecciona un valor']),
                ],
            ]
        );
    }
}
