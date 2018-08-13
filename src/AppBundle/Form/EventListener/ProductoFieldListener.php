<?php
/**
 * User: inrumi
 * Date: 6/27/18
 * Time: 17:22
 */

namespace AppBundle\Form\EventListener;


use AppBundle\Entity\Tienda\Producto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ProductoFieldListener implements EventSubscriberInterface
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
        $form = $event->getForm();
        $entrada = $event->getData();

        $producto = $entrada ? $entrada->getProducto() : null;

        $this->createProductoField($form, $producto ? $producto->getId() : null);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $productoId = array_key_exists('producto', $data) ? $data['producto'] : null;
        $this->createProductoField($event->getForm(), $productoId);
    }

    private function createProductoField(FormInterface $form, $productoId = null)
    {
        $producto = !$productoId ? [] : [$this->entityManager->getRepository(Producto::class)->find($productoId)];

        $form->add(
            'producto',
            EntityType::class,
            [
                'class' => Producto::class,
                'choices' => $producto
            ]
        );
    }
}
