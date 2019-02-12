<?php

function mf_ini_complemento_obrasarte10(&$datos)
{
    $alias_obrasarte['obrasarte10']['CaracteristicasDeObraoPieza'] = utf8_decode('CaracterísticasDeObraoPieza');
    mf_agrega_alias($alias_obrasarte);
}

function mf_complemento_obrasarte10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['obrasarte']['uri'] = 'http://www.sat.gob.mx/arteantiguedades';
    $__mf_namespaces__['obrasarte']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/arteantiguedades/obrasarteantiguedades.xsd';

    $atrs = mf_atributos_nodo($datos, 'obrasarte10');
    $xml = "<obrasarte:obrasarteantiguedades Version='1.0' $atrs />";
    return $xml;
}
