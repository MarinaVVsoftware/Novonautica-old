<?php

function mf_complemento_pagoenespecie10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['pagoenespecie']['uri'] = 'http://www.sat.gob.mx/pagoenespecie';
    $__mf_namespaces__['pagoenespecie']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/pagoenespecie/pagoenespecie.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<pagoenespecie:PagoEnEspecie Version='1.0' $atrs>";
	
    $xml .= "</pagoenespecie:PagoEnEspecie>";
    return $xml;
}
