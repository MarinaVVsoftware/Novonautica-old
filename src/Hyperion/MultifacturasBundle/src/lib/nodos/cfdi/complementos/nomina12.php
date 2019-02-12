<?php

function mf_ini_complemento_nomina12(&$datos)
{
    global $__mf_alias__;
	global $__mf_constantes__;
    // Lista de alias para Nomina v1.2
    //$alias_nomina12['nomina12']['Receptor']['Antiguedad'] = 'Antig�edad';
    $alias_nomina12['nomina12']['Receptor']['Antiguedad'] = utf8_decode('Antigüedad');
	$alias_nomina12['nomina12']['SeparacionIndemnizacion']['NumAnosServicio'] = utf8_decode('NumAñosServicio');
	$alias_nomina12['nomina12']['OtroPago']['CompensacionSaldosAFavor']['Ano'] = utf8_decode('Año');
    mf_agrega_alias($alias_nomina12);
}

function mf_complemento_nomina12($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['nomina12']['uri'] = 'http://www.sat.gob.mx/nomina12';
    $__mf_namespaces__['nomina12']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/nomina/nomina12.xsd';

    $atrs = mf_atributos_nodo($datos, 'nomina12');
    $xml = "<nomina12:Nomina Version='1.2' $atrs>";

    if(isset($datos['Emisor']))
    {
		$datosEmisor = $datos['Emisor'];
		
        $atrs = mf_atributos_nodo($datosEmisor, 'nomina12.Emisor');
        $xml .= "<nomina12:Emisor $atrs>";
        if(isset($datosEmisor['EntidadSNCF']))
        {
            $atrsentidad = mf_atributos_nodo($datosEmisor['EntidadSNCF'], 'nomina12.Emisor.EntidadSNCF');
            $xml .= "<nomina12:EntidadSNCF $atrsentidad />";
        }
        $xml .= "</nomina12:Emisor>";
    }
    if(isset($datos['Receptor']))
    {
        $atrs = mf_atributos_nodo($datos['Receptor'], 'nomina12.Receptor');
        $xml .= "<nomina12:Receptor $atrs>";
        if(isset($datos['Receptor']['SubContratacion']))
        {
            foreach($datos['Receptor']['SubContratacion'] as $idx => $entidad)
            {
                $atrs = mf_atributos_nodo($entidad, 'nomina12.Receptor.SubContratacion');
                $xml .= "<nomina12:SubContratacion $atrs />";
            }
        }
        $xml .= "</nomina12:Receptor>";
    }
    if(isset($datos['Percepciones']))
    {
        $atrs = mf_atributos_nodo($datos['Percepciones'], 'nomina12.Percepciones');
        $xml .= "<nomina12:Percepciones $atrs>";
        foreach($datos['Percepciones'] as $idx =>$entidad)
        {
            if(is_array($datos['Percepciones'][$idx]) && is_int($idx))
            {
                $atrs = mf_atributos_nodo($datos['Percepciones'][$idx], 'nomina12.Percepcion');
                $xml .= "<nomina12:Percepcion $atrs >";
                if(isset($datos['Percepciones'][$idx]['AccionesOTitulos']))
                {
                    $atrs = mf_atributos_nodo($datos['Percepciones'][$idx]['AccionesOTitulos'], 'nomina12.Percepcion.AccionesOTitulos');
                    $xml .= "<nomina12:AccionesOTitulos $atrs/>";
                }
                if(isset($datos['Percepciones'][$idx]['HorasExtra']))
                {
                    foreach($datos['Percepciones'][$idx]['HorasExtra']  as $idx2 => $entidad2)
                    {
                        $atrs = mf_atributos_nodo($entidad2, 'nomina12.Percepcion.HoraExtra');
                        $xml .= "<nomina12:HorasExtra $atrs/>";
                    }
                }
                $xml .= "</nomina12:Percepcion>";
            }
        }
        if(isset($datos['Percepciones']['JubilacionPensionRetiro']))
        {
            $atrsentidad = mf_atributos_nodo($datos['Percepciones']['JubilacionPensionRetiro'], 'nomina12.JubilacionPensionRetiro');
            $xml .= "<nomina12:JubilacionPensionRetiro $atrsentidad />";
        }
        if(isset($datos['Percepciones']['SeparacionIndemnizacion']))
        {
            $atrsentidad = mf_atributos_nodo($datos['Percepciones']['SeparacionIndemnizacion'], 'nomina12.SeparacionIndemnizacion');
            $xml .= "<nomina12:SeparacionIndemnizacion $atrsentidad />";
        }
        $xml .= "</nomina12:Percepciones>";
    }
    if(isset($datos['Deducciones']))
    {
        $atrs = mf_atributos_nodo($datos['Deducciones'], 'nomina12.Deducciones');
        $xml .= "<nomina12:Deducciones $atrs>";
        foreach($datos['Deducciones'] as $idx => $entidad)
        {
            if(is_array($datos['Deducciones'][$idx]))
            {
                $atrs = mf_atributos_nodo($datos['Deducciones'][$idx], 'nomina12.Deduccion');
                $xml .= "<nomina12:Deduccion $atrs />";
            }
        }
        $xml .= "</nomina12:Deducciones>";
    }
    if(isset($datos['OtrosPagos']))
    {
        $atrs = mf_atributos_nodo($datos['OtrosPagos'], 'nomina12.OtrosPagos');
        $xml .= "<nomina12:OtrosPagos $atrs>";
        foreach($datos['OtrosPagos'] as $idx => $entidad)
        {
            if(is_array($datos['OtrosPagos'][$idx]))
            {
                $atrs = mf_atributos_nodo($entidad, 'nomina12.OtroPago');
                $xml .= "<nomina12:OtroPago $atrs >";
                if(isset($datos['OtrosPagos'][$idx]['SubsidioAlEmpleo']))
                {
                    $atrsentidad = mf_atributos_nodo($datos['OtrosPagos'][$idx]['SubsidioAlEmpleo'], 'nomina12.OtrosPagos.SubsidioAlEmpleo');
                    $xml .= "<nomina12:SubsidioAlEmpleo $atrsentidad />";
                }
                if(isset($datos['OtrosPagos'][$idx]['CompensacionSaldosAFavor']))
                {
                    $atrsentidad = mf_atributos_nodo($datos['OtrosPagos'][$idx]['CompensacionSaldosAFavor'], 'nomina12.OtroPago.CompensacionSaldosAFavor');
                    $xml .= "<nomina12:CompensacionSaldosAFavor $atrsentidad />";
                }
                $xml .= "</nomina12:OtroPago>";
            }
        }
        $xml .= "</nomina12:OtrosPagos>";
    }
    if(isset($datos['Incapacidades']))
    {
        $atrs = mf_atributos_nodo($datos['Incapacidades'], 'nomina12.Incapacidades');
        $xml .= "<nomina12:Incapacidades $atrs >";
        foreach($datos['Incapacidades'] as $idx => $entidad)
        {
            if(is_array($datos['Incapacidades'][$idx]))
            {
                $atrs = mf_atributos_nodo($datos['Incapacidades'][$idx], 'nomina12.Incapacidad');
                $xml .= "<nomina12:Incapacidad $atrs />";
            }
        }
        $xml .= "</nomina12:Incapacidades>";
    }
    $xml .= "</nomina12:Nomina>";
    return $xml;
}

//$xml = mf_complemento_nomina($datos['nomina12']);
//echo $xml;
