<?php

function mf_ini_complemento_certificadodestruccion10(&$datos)
{
    global $__mf_alias__;
	global $__mf_constantes__;
    // Lista de alias para Nomina v1.2
    $alias_certificadodestruccion10['certificadodestruccion10']['VehiculoDestruido']['Ano'] = utf8_decode('Año');
    mf_agrega_alias($alias_certificadodestruccion10);
}

function mf_complemento_certificadodestruccion10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['destruccion']['uri'] = 'http://www.sat.gob.mx/certificadodestruccion';
    $__mf_namespaces__['destruccion']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/certificadodestruccion/certificadodedestruccion.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<destruccion:certificadodedestruccion Version='1.0' $atrs>";
	
	if(isset($datos['VehiculoDestruido']))
    {
        $atrsentidad = mf_atributos_nodo($datos['VehiculoDestruido'], 'certificadodestruccion10.VehiculoDestruido');
        $xml .= "<destruccion:VehiculoDestruido $atrsentidad/>";
    }
	if(isset($datos['InformacionAduanera']))
    {
        $atrsentidad = mf_atributos_nodo($datos['InformacionAduanera']);
        $xml .= "<destruccion:InformacionAduanera $atrsentidad/>";
    }
    $xml .= "</destruccion:certificadodedestruccion>";
    return $xml;
}
