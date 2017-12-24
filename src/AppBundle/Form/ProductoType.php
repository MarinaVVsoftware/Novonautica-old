<?php

namespace AppBundle\Form;

use AppBundle\Entity\Producto;
use AppBundle\Entity\Producto\Categoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('precio', MoneyType::class, [
                'label' => 'Precio (MXN)',
                'divisor' => 100,
                'currency' => 'MXN',
                'grouping' => true
            ])
            ->add('ucp', TextType::class, ['label' => 'UCP'])
            ->add('modelo')
            ->add('unidad')
            ->add('descripcion', TextareaType::class, [
                'attr' => ['rows' => 10]
            ])
            ->add('imagenFile', VichImageType::class, [
                'label' => 'Imagen',
                'download_label' => 'Ver brochure',
                'delete_label' => '¿Eliminar brochure?',
                'required' => false
            ])
            ->add('fichaTecnicaFile', VichFileType::class, [
                'label' => 'Ficha Tecnica',
                'download_label' => 'Ver brochure',
                'delete_label' => '¿Eliminar brochure?',
                'required' => false
            ])
            ->add('marca')
            ->add('categoria', EntityType::class, [
                'class' => 'AppBundle\Entity\Producto\Categoria'
            ]);

        $formModifier = function (FormInterface $form, Categoria $categoria = null) {
            /** @var Categoria $categoria */
            $modelos = $categoria ? $categoria->getSubcategorias() : [];

            $form->add('subcategoria', EntityType::class, [
                'class' => 'AppBundle\Entity\Producto\Subcategoria',
                'choices' => $modelos
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var Producto $producto */
                $producto = $event->getData();
                $formModifier($event->getForm(), $producto->getCategoria());
            });

        $builder->get('categoria')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                /** @var Categoria $categoria */
                $categoria = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $categoria);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Producto'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_producto';
    }


}
