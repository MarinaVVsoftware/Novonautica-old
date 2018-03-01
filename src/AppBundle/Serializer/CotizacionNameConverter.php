<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/24/18
 * Time: 13:55
 */

namespace AppBundle\Serializer;


use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class CotizacionNameConverter implements NameConverterInterface
{

    /**
     * Converts a property name to its normalized value.
     *
     * @param string $propertyName
     *
     * @return string
     */
    public function normalize($propertyName)
    {
        if (preg_match('/servicios/', $propertyName)) {
            return 'conceptos';
        } else if (preg_match('/astilleroserviciobasico/', $propertyName)) {
            return 'serviciobasico';
        }

        return $propertyName;
    }

    /**
     * Converts a property name to its denormalized value.
     *
     * @param string $propertyName
     *
     * @return string
     */
    public function denormalize($propertyName)
    {
        return $propertyName;
    }
}