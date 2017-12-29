<?php

function mf_complemento_acreditamientoieps10($datos)
{
    // Se agregan los namespaces
    global $__mf_namespaces__;
    $__mf_namespaces__['aieps']['uri'] = 'http://www.sat.gob.mx/acreditamiento';
    $__mf_namespaces__['aieps']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/acreditamiento/AcreditamientoIEPS10.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<aieps:acreditamientoIEPS Version='1.0' $atrs>";
    $xml .= "</aieps:acreditamientoIEPS>";
    return $xml;
}
