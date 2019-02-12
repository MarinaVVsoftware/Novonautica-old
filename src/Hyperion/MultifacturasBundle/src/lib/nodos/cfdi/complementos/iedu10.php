<?php

function mf_complemento_iedu10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['iedu']['uri'] = 'http://www.sat.gob.mx/iedu';
    $__mf_namespaces__['iedu']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/iedu/iedu.xsd';

    $atrs = mf_atributos_nodo($datos, '');//$datos, '');
    $xml = "<iedu:instEducativas version='1.0' $atrs/>";
    return $xml;
}