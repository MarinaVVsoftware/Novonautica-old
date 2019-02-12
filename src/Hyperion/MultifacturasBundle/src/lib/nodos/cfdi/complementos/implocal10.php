<?php

function mf_complemento_implocal10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['implocal']['uri'] = 'http://www.sat.gob.mx/implocal';
    $__mf_namespaces__['implocal']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd';

    $atrs = mf_atributos_nodo($datos);

    $xml = "<implocal:ImpuestosLocales version='1.0' $atrs>";

    if(isset($datos['TrasladosLocales']))
    {
        foreach($datos['TrasladosLocales'] as $idx => $trasladoLocal)
        {
            $atrstra = mf_atributos_nodo($trasladoLocal);
            $xml .= "<implocal:TrasladosLocales $atrstra />";
        }
    }
	if(isset($datos['RetencionesLocales']))
    {
        foreach($datos['RetencionesLocales'] as $idx => $retencionLocal)
        {
            $atrsret = mf_atributos_nodo($retencionLocal);
            $xml .= "<implocal:RetencionesLocales $atrsret/>";
        }
    }
    $xml .= "</implocal:ImpuestosLocales>";
    return $xml;
}