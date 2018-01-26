<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/24/18
 * Time: 15:06
 */

namespace AppBundle\Serializer;


use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class NotNullObjectNormalizer extends ObjectNormalizer
{
    public function normalize($object, $format = null, array $context = array())
    {
        $data = parent::normalize($object, $format, $context);

        return array_filter($data, function ($value) {
           return null !== $value;
        });
    }
}