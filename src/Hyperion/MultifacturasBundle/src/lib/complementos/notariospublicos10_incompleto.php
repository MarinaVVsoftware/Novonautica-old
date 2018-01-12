<?php

function mf_complemento_notariospublicos10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['notariospublicos']['uri'] = 'http://www.sat.gob.mx/notariospublicos';
    $__mf_namespaces__['notariospublicos']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/notariospublicos/notariospublicos.xsd';

	$atrs = mf_atributos_nodo($datos);
    $xml = "<notariospublicos:NotariosPublicos Version='1.0' $atrs>";

    if(isset($datos['DescInmuebles']))
    {
		$atrs = mf_atributos_nodo($datos['DescInmuebles']);
		$xml .= "<notariospublicos:DescInmuebles $atrs>";
        foreach($datos['DescInmuebles'] as $idx => $entidad)
        {
			if(is_array($datos['DescInmuebles'][$idx]))
			{
				$atrs = mf_atributos_nodo($entidad);
				$xml .= "<notariospublicos:DescInmueble $atrs />";
			}
        }
		$xml .= "</notariospublicos:DescInmuebles>";
    }
	if(isset($datos['DatosOperacion']))
    {
		$atrs = mf_atributos_nodo($datos['DatosOperacion']);

		$xml .= "<notariospublicos:DatosOperacion $atrs/>";
	}
	if(isset($datos['DatosNotario']))
    {
        $atrs = mf_atributos_nodo($datos['DatosNotario']);
		$xml .= "<notariospublicos:DatosNotario $atrs/>";
	}
	if(isset($datos['DatosEnajenante']))
    {
		$atrs = mf_atributos_nodo($datos['DatosEnajenante']);
		$xml .= "<notariospublicos:DatosEnajenante $atrs>";
		if(isset($datos['DatosEnajenante']['DatosUnEnajenante']))
		{
			$atrs = mf_atributos_nodo($datos['DatosEnajenante']['DatosUnEnajenante']);
			$xml .= "<notariospublicos:DatosUnEnajenante $atrs/>";
		}
		if(isset($datos['DatosEnajenantesCopSC']))
		{
			$atrs = mf_atributos_nodo($datos['DatosEnajenante']['DatosEnajenantesCopSC']);
			$xml .= "<notariospublicos:DatosEnajenantesCopSC $atrs>";
			foreach($datos['DatosEnajenantesCopSC'] as $idx => $entidad)
			{
				if(is_array($datos['DatosEnajenante']['DatosEnajenantesCopSC'][$idx]))
				{
					$atrs = mf_atributos_nodo($entidad);
					$xml .= "<notariospublicos:DatosEnajenanteCopSC $atrs />";
				}
			}
			$xml .= "</notariospublicos:DatosEnajenantesCopSC>";
		}
		$xml .= "</notariospublicos:DatosEnajenante>";
	}
	if(isset($datos['DatosAdquiriente']))
    {
		$atrs = mf_atributos_nodo($datos['DatosAdquiriente']);
		$xml .= "<notariospublicos:DatosAdquiriente $atrs>";
		if(isset($datos['DatosAdquiriente']['DatosUnAdquiriente']))
		{
			$atrs = mf_atributos_nodo($datos['DatosAdquiriente']['DatosUnAdquiriente']);
			$xml .= "<notariospublicos:DatosUnAdquiriente $atrs/>";
		}
		if(isset($datos['DatosAdquiriente']['DatosAdquirientesCopSC']))
		{
			$atrs = mf_atributos_nodo($datos['DatosAdquiriente']['DatosAdquirientesCopSC']);
			$xml .= "<notariospublicos:DatosAdquirientesCopSC $atrs>";
			foreach($datos['DatosAdquiriente']['DatosAdquirientesCopSC'] as $idx => $entidad)
			{
				if(is_array($datos['DatosAdquiriente']['DatosAdquirientesCopSC'][$idx]))
				{
					$atrs = mf_atributos_nodo($entidad);
					$xml .= "<notariospublicos:DatosAdquirienteCopSC $atrs />";
				}
			}
			$xml .= "</notariospublicos:DatosAdquirientesCopSC>";
		}
		$xml .= "</notariospublicos:DatosAdquiriente>";
	}

    $xml .= "</notariospublicos:NotariosPublicos>";
    return $xml;
}