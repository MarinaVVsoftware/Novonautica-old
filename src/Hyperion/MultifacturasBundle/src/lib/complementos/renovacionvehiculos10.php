<?php

function mf_complemento_renovacionvehiculos10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['decreto']['uri'] = 'http://www.sat.gob.mx/renovacionysustitucionvehiculos';
    $__mf_namespaces__['decreto']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/renovacionysustitucionvehiculos/renovacionysustitucionvehiculos.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<decreto:renovacionysustitucionvehiculos Version='1.0' $atrs>";
	
	if(isset($datos['DecretoRenovVehicular']))
    {
        $atrsentidad = mf_atributos_nodo($datos['DecretoRenovVehicular']);
        $xml .= "<decreto:DecretoRenovVehicular $atrsentidad>";
		foreach($datos['DecretoRenovVehicular'] as $idx =>$entidad)
		{
			if(is_array($datos['DecretoRenovVehicular'][$idx]) && is_int($idx))
			{
				$atrs = mf_atributos_nodo($datos['DecretoRenovVehicular'][$idx]);
				$xml .= "<decreto:VehiculosUsadosEnajenadoPermAlFab $atrs/>";
			}
		}
		if(isset($datos['DecretoRenovVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']))
		{
			$atrsentidad = mf_atributos_nodo($datos['DecretoRenovVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']);
			$xml .= "<decreto:VehiculoNuvoSemEnajenadoFabAlPerm $atrsentidad/>";
		}
		$xml .= "</decreto:DecretoRenovVehicular>";
	}
	if(isset($datos['DecretoSustitVehicular']))
    {
        $atrsentidad = mf_atributos_nodo($datos['DecretoSustitVehicular']);
        $xml .= "<decreto:DecretoSustitVehicular $atrsentidad>";
		if(isset($datos['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']))
		{
			$atrsentidad = mf_atributos_nodo($datos['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']);
			$xml .= "<decreto:VehiculoUsadoEnajenadoPermAlFab $atrsentidad/>";
		}
		if(isset($datos['DecretoSustitVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']))
		{
			$atrsentidad = mf_atributos_nodo($datos['DecretoSustitVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']);
			$xml .= "<decreto:VehiculoNuvoSemEnajenadoFabAlPerm $atrsentidad/>";
		}
		$xml .= "</decreto:DecretoSustitVehicular>";
    }
    $xml .= "</decreto:renovacionysustitucionvehiculos>";
    return $xml;
}
