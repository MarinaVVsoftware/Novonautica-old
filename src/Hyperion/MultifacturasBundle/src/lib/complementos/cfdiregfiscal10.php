<?php

function mf_complemento_cfdiregfiscal10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['registrofiscal']['uri'] = 'http://www.sat.gob.mx/registrofiscal';
    $__mf_namespaces__['registrofiscal']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/cfdiregistrofiscal/cfdiregistrofiscal.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<registrofiscal:CFDIRegistroFiscal Version='1.0' $atrs>";
	
    $xml .= "</registrofiscal:CFDIRegistroFiscal>";
    return $xml;
}
