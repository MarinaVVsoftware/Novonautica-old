<?php
/**
 * User: inrumi
 * Date: 6/27/18
 * Time: 17:22
 */

namespace AppBundle\Form\EventListener;


use AppBundle\Entity\Tienda\Producto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ProductoFieldListener implements EventSubscriberInterface
{
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $this->createProductoField($event->getForm());
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $productoId = array_key_exists('producto', $data) ? $data['producto'] : null;
        $this->createProductoField($event->getForm(), $productoId);
    }

    private function createProductoField(FormInterface $form, $productoId = null)
    {
        $formOptions = [
            'class' => Producto::class,
            'placeholder' => 'Seleccione un producto',
            'choices' => [$productoId]
        ];

        $form->add(
            'producto',
            EntityType::class,
            $formOptions
        );
    }
}
