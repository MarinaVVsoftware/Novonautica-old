<?php

function mf_complemento_terceros11($datos)
{
	// Variable para los namespaces xml
	global $__mf_namespaces__;
	$__mf_namespaces__['terceros']['uri'] = 'http://www.sat.gob.mx/terceros';
	$__mf_namespaces__['terceros']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/terceros/terceros11.xsd';

	$atrs = mf_atributos_nodo($datos);
    $xml = "<terceros:PorCuentadeTerceros version='1.1' $atrs>";

    if(isset($datos['InformacionFiscalTercero']))
    {
		$atrs = mf_atributos_nodo($datos['InformacionFiscalTercero']);
		$xml .= "<terceros:InformacionFiscalTercero $atrs />";
    }
	if(isset($datos['InformacionAduanera']))
    {
		$atrs = mf_atributos_nodo($datos['InformacionAduanera']);
		$xml .= "<terceros:InformacionAduanera $atrs />";
    }
	if(isset($datos['Parte']))
    {
		foreach($datos['Parte'] as $idx =>$entidad)
		{
			if(is_array($datos['Parte'][$idx]) && is_int($idx))
			{
				$atrs = mf_atributos_nodo($datos['Parte'][$idx]);
				$xml .= "<terceros:Parte $atrs >";
				if(isset($datos['Parte'][$idx]['InformacionAduanera']))
				{
					foreach($datos['Parte'][$idx]['InformacionAduanera']  as $idx2 => $entidad2)
					{
						if(is_array($datos['Parte'][$idx]['InformacionAduanera'][$idx2]) && is_int($idx))
						{
							$atrs = mf_atributos_nodo($entidad2);
							$xml .= "<terceros:InformacionAduanera $atrs/>";
						}
					}
				}
				$xml .= "</terceros:Parte>";
			}	
		}
	}
	if(isset($datos['CuentaPredial']))
	{
		$atrsentidad = mf_atributos_nodo($datos['CuentaPredial']);
		$xml .= "<terceros:CuentaPredial $atrsentidad />";
	}
	if(isset($datos['Impuestos']))
    {
		$atrs = mf_atributos_nodo($datos['Impuestos']);
		$xml .= "<terceros:Impuestos $atrs>";
		if(isset($datos['Impuestos']['Retenciones']))
		{
			$atrs = mf_atributos_nodo($datos['Impuestos']['Retenciones']);
			$xml .= "<terceros:Retenciones $atrs>";
			foreach($datos['Impuestos']['Retenciones'] as $idx => $entidad)
			{
				if(is_array($datos['Impuestos']['Retenciones'][$idx]))
				{
					$atrs = mf_atributos_nodo($entidad);
					$xml .= "<terceros:Retencion $atrs />";
				}
			}
			$xml .= "</terceros:Retenciones>";
		}
		if(isset($datos['Impuestos']['Traslados']))
		{
			$atrs = mf_atributos_nodo($datos['Impuestos']['Traslados']);
			$xml .= "<terceros:Traslados $atrs>";
			foreach($datos['Impuestos']['Traslados'] as $idx => $entidad)
			{
				if(is_array($datos['Impuestos']['Traslados'][$idx]))
				{
					$atrs = mf_atributos_nodo($entidad);
					$xml .= "<terceros:Traslado $atrs />";
				}
			}
			$xml .= "</terceros:Traslados>";
		}
		$xml .= "</terceros:Impuestos>";
	}
    $xml .= "</terceros:PorCuentadeTerceros>";
    return $xml;
}