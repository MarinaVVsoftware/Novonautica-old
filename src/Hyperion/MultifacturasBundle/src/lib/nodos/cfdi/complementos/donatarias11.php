<?php

function mf_complemento_donatarias11($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['donat']['uri'] = 'http://www.sat.gob.mx/donat';
    $__mf_namespaces__['donat']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/donat/donat11.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<donat:Donatarias version='1.1' $atrs>";
	
    $xml .= "</donat:Donatarias>";
    return $xml;
}
