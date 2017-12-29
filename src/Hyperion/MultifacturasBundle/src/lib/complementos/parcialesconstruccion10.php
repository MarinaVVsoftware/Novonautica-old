<?php

function mf_complemento_parcialesconstruccion10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['servicioparcial']['uri'] = 'http://www.sat.gob.mx/servicioparcialconstruccion';
    $__mf_namespaces__['servicioparcial']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/servicioparcialconstruccion/servicioparcialconstruccion.xsd';

    $atrsparciales = "Version='1.0' " . mf_atributos_nodo($datos);
    $inmueble = '';
    if(isset($datos['Inmueble']))
    {
        $atrsinmueble = mf_atributos_nodo($datos['Inmueble']);
        $inmueble = "<servicioparcial:Inmueble $atrsinmueble/>";
    }
    $xml = "<servicioparcial:parcialesconstruccion $atrsparciales>$inmueble</servicioparcial:parcialesconstruccion>";
    return $xml;
}