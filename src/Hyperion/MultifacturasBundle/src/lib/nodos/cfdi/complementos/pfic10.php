<?php

function mf_complemento_pfic10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['pfic']['uri'] = 'http://www.sat.gob.mx/pfic';
    $__mf_namespaces__['pfic']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/pfic/pfic.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<pfic:PFintegranteCoordinado version='1.0' $atrs>";
    $xml .= "</pfic:PFintegranteCoordinado>";
    return $xml;
}
