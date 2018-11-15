<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/14/18
 * Time: 1:41 PM
 */

namespace AppBundle\Extra;


use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\Combustible;
use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\Tienda\Venta;
use Doctrine\Common\Persistence\ObjectManager;

class FacturacionHelper
{
    public static function getCotizaciones(ObjectManager $manager, $emisor, $cliente, $fecha)
    {
        $inicio = (\DateTime::createFromFormat('Y-m-d', $fecha))->modify('first day of this month');
        $fin = (clone $inicio)->modify('last day of this month');

        $cotizacionRepository = FacturacionHelper::getCotizacionRepository($manager, $emisor);

        return $cotizacionRepository->getCotizacionesFromCliente($cliente, $inicio, $fin);
    }

    public static function getCotizacionRepository(ObjectManager $manager, $emisor)
    {
        switch ($emisor) {
            case 3:
                $repository = $manager->getRepository(MarinaHumedaCotizacion::class);
                break;
            case 4:
                $repository = $manager->getRepository(Combustible::class);
                break;
            case 5:
                $repository = $manager->getRepository(AstilleroCotizacion::class);
                break;
            case 7:
                $repository = $manager->getRepository(Venta::class);
                break;
            default:
                $repository = null;
        }

        return $repository;
    }
}
