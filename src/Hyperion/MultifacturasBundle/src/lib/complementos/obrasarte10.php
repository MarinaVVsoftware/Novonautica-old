<?php

function mf_complemento_obrasarte10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['obrasarte']['uri'] = 'http://www.sat.gob.mx/arteantiguedades';
    $__mf_namespaces__['nomiobrasartena12']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/arteantiguedades/obrasarteantiguedades.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<obrasarte:obrasarteantiguedades Version='1.0' $atrs>";
	
    $xml .= "</obrasarte:obrasarteantiguedades>";
    return $xml;
}
