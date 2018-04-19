<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 4/19/18
 * Time: 13:19
 */

namespace AppBundle\Entity\Astillero\Contratista\Actividad;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

class FotoListener
{
    public function postRemove(Foto $foto, LifecycleEventArgs $eventArgs)
    {
        $fs = new Filesystem();
        $fs->remove("../web/uploads/actividad_fotos/{$foto->getBasename()}");
    }
}