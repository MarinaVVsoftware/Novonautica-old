<?php

function mf_complemento_divisas10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['divisas']['uri'] = 'http://www.sat.gob.mx/divisas';
    $__mf_namespaces__['divisas']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/divisas/Divisas.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<divisas:Divisas version='1.0' $atrs>";
	
    $xml .= "</divisas:Divisas>";
    return $xml;
}
