<?php

function ___validasumas($datos)
{
    // Se inicializa el SDK
    init_sdk($datos);

    // Version de CFDI
    selecciona_version_cfdi($datos);

    // Se cargan los alias
    mf_carga_utileria('alias');

    // Se obtiene la plantilla generada
    $string_xml = mf_carga_nodo('cfdi', $datos);

    // Se revisa si esta en base64
    $es_xml = is_int(strpos($string_xml, 'cfdi'));
    if(!$es_xml)
    {
        $string_xml = base64_decode($string_xml);
    }

	// Valores de inicializacion
    $datos = cfdi2array($string_xml);

    // Arreglo de Respuesta
	$res = array(
        'tiene_retenciones' => false,
        'tiene_traslados' => false,
        'log' => array()
    );

    // Se procesan los nodos
	nodo_conceptos($datos, $res);
    nodo_impuestos($datos, $res);
    nodo_comprobante($datos, $res);

    // Se devuelve el resultado
	return $res;
}

function nodo_impuestos($datos, &$res)
{

    if(isset($datos['impuestos']))
    {
        // Se verifica que existan los campos
        $impuestos = $datos['impuestos'];
        if(!isset($impuestos['TotalImpuestosRetenidos']) && $res['tiene_retenciones'] == true)
        {
            $res['log'][] = sprintf('ERROR: Falta campo factura.TotalImpuestosRetenidos.');
        }

        if(!isset($impuestos['TotalImpuestosTrasladados']) && $res['tiene_traslados'] == true)
        {
            $res['log'][] = sprintf('ERROR: Falta campo factura.TotalImpuestosTrasladados.');
        }

        // Se procesan los traslados
        if(isset($datos['impuestos']['translados']))
        {
            $traslados = $datos['impuestos']['translados'];

            foreach($traslados as $idx => $traslado)
            {
                // Se valida que existan los campos
                if(!isset($traslados[$idx]['Impuesto']))
                {
                    $res['log'][] = sprintf('ERROR: Falta campo factura.impuestos.translados[%s].impuesto', $idx);
                }
                if(!isset($traslados[$idx]['TasaOCuota']))
                {
                    $res['log'][] = sprintf('ERROR: Falta campo factura.impuestos.translados[%s].TasaOCuota', $idx);
                }
                if(!isset($traslados[$idx]['Importe']))
                {
                    $res['log'][] = sprintf('ERROR: Falta campo factura.impuestos.translados[%s].importe', $idx);
                }
                if(!isset($traslados[$idx]['TipoFactor']))
                {
                    $res['log'][] = sprintf('ERROR: Falta campo factura.impuestos.translados[%s].TipoFactor', $idx);
                }

                // Se suman los importes de los impuestos
                if(isset($traslado['Importe']))
                {
                    $clave = sprintf('impuestos_traslados_%s_%s_%s', $traslado['Impuesto'], $traslado['TipoFactor'], $traslado['TasaOCuota']);
                    //$res[$clave] += $traslado['Importe'];
                    $res['traslados'][$traslado['Impuesto']][$traslado['TipoFactor']][$traslado['TasaOCuota']] += $traslado['Importe'];

                    // Total de traslados
                    $res['impuestos_traslados'] += $traslado['Importe'];
                }
            }
        }

        // Se procesan las retenciones
        if(isset($datos['impuestos']['retenciones']))
        {
            $retenciones = $datos['impuestos']['retenciones'];
            //print_r($retenciones);die();
            foreach($retenciones as $idx => $retencion)
            {
                // Se valida que existan los campos
                if(!isset($retenciones[$idx]['Impuesto']))
                {
                    $res['log'][] = sprintf('ERROR: Falta campo factura.impuestos.retenciones[%s].Impuesto', $idx);
                }
                if(!isset($retenciones[$idx]['Importe']))
                {
                    $res['log'][] = sprintf('ERROR: Falta campo factura.impuestos.retenciones[%s].Importe', $idx);
                }

                // Se suman los importes de los impuestos
                if(isset($retencion['Importe']))
                {
                    //$clave = sprintf('impuestos_retenciones_%s', $retencion['Impuesto']);
                    //$res[$clave] += $retencion['Importe'];
                    $res['retenciones'][$retencion['Impuesto']] += $retencion['Importe'];

                    // Total de traslados
                    $res['impuestos_retenciones'] += $retencion['Importe'];
                }
            }
        }
    }
}

/**
 * @param $datos array
 * @param $res array
 */
function nodo_conceptos($datos, &$res)
{
	$conceptos = $datos['conceptos'];
	foreach($conceptos as $idx_concepto => $concepto)
	{
		// Se valida que existan los campos necesarios
		if(!isset($concepto['Importe']))
		{
            $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].importe', $idx_concepto);
		}
        if(!isset($concepto['Cantidad']))
        {
            $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].cantidad', $idx_concepto);
        }
        if(!isset($concepto['ValorUnitario']))
        {
            $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].valorunitario', $idx_concepto);
        }

        // Se valida que el importe sea igual a cantidad * valor_uitario
        if($concepto['Importe'] != ($concepto['Cantidad'] * $concepto['ValorUnitario']))
        {
            $res['log'][] = sprintf('ERROR: Concepto[%s].importe debe ser igual a Concepto[%s].cantidad * Concepto[%s].valorunitario', $idx_concepto, $idx_concepto, $idx_concepto);
        }

        // Se suman los importes (subtotal)
        $res['importes_conceptos'] += $concepto['Importe'];
		
		// Se suman los traslados de los conceptos
        if(isset($concepto['Impuestos']['Traslados']))
        {
            $res['tiene_traslados'] = true;
            $traslados = $concepto['Impuestos']['Traslados'];
            foreach($traslados as $idx_traslado => $traslado)
            {
                if(!isset($traslado['Base']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Traslado[%s].Base', $idx_concepto, $idx_traslado);
                }
                if(!isset($traslado['Impuesto']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Traslado[%s].Impuesto', $idx_concepto, $idx_traslado);
                }
                if(!isset($traslado['TipoFactor']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Traslado[%s].TipoFactor', $idx_concepto, $idx_traslado);
                }
                if(!isset($traslado['TasaOCuota']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Traslado[%s].TasaOCuota', $idx_concepto, $idx_traslado);
                }
                if(!isset($traslado['Importe']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Traslado[%s].Importe', $idx_concepto, $idx_traslado);
                }

                // Se verifica que el campo base sea igual al campo importe del concepto
                if($concepto['Importe'] != $traslado['Base'])
                {
                    $res['log'][] = sprintf('ERROR: El campo Concepto[%s].Traslado[%s].Base debe ser igual a Concepto[%s].importe', $idx_concepto, $idx_traslado, $idx_concepto);
                }

                // Se suman los importes de los impuestos
                if(isset($traslado['Importe']))
                {
                    $clave = sprintf('traslados_%s_%s_%s', $traslado['Impuesto'], $traslado['TipoFactor'], $traslado['TasaOCuota']);
                    $res[$clave] += $traslado['Importe'];

                    $res['conceptos_traslados'] += $traslado['Importe'];
                }
            }
        }
        else
        {
            $res['log'][] = sprintf('INFO: No se especificaron Traslados en el Concepto[%s] (Es opcional)', $idx_concepto);
        }

        // Se suman las retenciones de los conceptos
        if(isset($concepto['Impuestos']['Retenciones']))
        {
            $res['tiene_retenciones'] = true;
            $retenciones = $concepto['Impuestos']['Retenciones'];
            foreach($retenciones as $idx_retencion => $retencion)
            {
                if(!isset($retencion['Base']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Retenciones[%s].Base', $idx_concepto, $idx_retencion);
                }
                if(!isset($retencion['Impuesto']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Retenciones[%s].Impuesto', $idx_concepto, $idx_retencion);
                }
                if(!isset($retencion['TipoFactor']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Retenciones[%s].TipoFactor', $idx_concepto, $idx_retencion);
                }
                if(!isset($retencion['TasaOCuota']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Retenciones[%s].TasaOCuota', $idx_concepto, $idx_retencion);
                }
                if(!isset($retencion['Importe']))
                {
                    $res['log'][] = sprintf('INFO: Falta campo Concepto[%s].Retenciones[%s].Importe', $idx_concepto, $idx_retencion);
                }

                // Se verifica que el campo base sea igual al campo importe del concepto
                if($concepto['importe'] != $retencion['Base'])
                {
                    $res['log'][] = sprintf('ERROR: El campo Concepto[%s].Retenciones[%s].Base debe ser igual a Concepto[%s].importe', $idx_concepto, $idx_retencion, $idx_concepto);
                }

                // Se suman los importes de los impuestos
                if(isset($retencion['Importe']))
                {
                    $clave = sprintf('retenciones_%s_%s_%s', $retencion['Impuesto'], $retencion['TipoFactor'], $retencion['TasaOCuota']);
                    //$res[$clave] += $retencion['Importe'];
                    $res[$retencion['Impuesto']] += $retencion['Importe'];

                    // Total de retenciones
                    $res['total_retenciones'] += $retencion['Importe'];
                }
            }
        }
        else
        {
            $res['log'][] = sprintf('INFO: No se especificaron Retenciones en el Concepto[%s] (Es opcional)', $idx_concepto);
        }
	}
}

/**
 * @param $datos array
 * @param $res array
 */
function nodo_comprobante($datos, &$res)
{
    // Se valida el SubTotal
    if(!isset($datos['factura']['SubTotal']))
    {
        $res['log'][] = sprintf('ERROR: Falta campo factura.subtotal');
    }
    else
    {
        // Se valida que el subtotal coincida con la suma de los importes de los impuestos
        if($datos['factura']['SubTotal'] != $res['importes_conceptos'])
        {
            $res['log'][] = sprintf('ERROR: El campo factura.subtotal(%s) no corresponde con la suma de los importes de los conceptos(%s)', $datos['factura']['SubTotal'], $res['importes_conceptos']);
        }
    }

    // Se valida que existan los campos necesarios
    if(!isset($datos['factura']['Descuento']))
    {
        $res['log'][] = sprintf('INFO: Falta campo factura.descuento(Campo opcional)');
    }
    else
    {
        // Se valida que Descuento sea menos o igual al SubTotal
        if(floatval($datos['factura']['Descuento']) > floatval($datos['factura']['SubTotal']))
        {
            $res['log'][] = sprintf('ERROR: El campo factura.descuento(%s) debe ser menor o igual al campo factura.subtotal(%s)', floatval($datos['factura']['Descuento']), floatval($datos['factura']['SubTotal']));
        }

        // Se valida que el campo descuento sea igual a las retenciones
        if(isset($res['impuestos_retenciones']))
        {
            if($datos['factura']['Descuento'] != $res['impuestos_retenciones'])
            {
                $res['log'][] = sprintf('ERROR: El campo factura.descuento(%s) debe ser igual a la suma de las retenciones(%s)', floatval($datos['factura']['Descuento']), floatval($res['impuestos_retenciones']));
            }
        }
        else
        {
            $descuento = floatval($datos['factura']['Descuento']);
            if($descuento != 0 && $res['tiene_retenciones'] == false)
            {
                $res['log'][] = sprintf('ERROR: Si no se tienen retenciones, se debe omitir el campo factura.descuento(%s) o debe ser igual cero', $descuento);
            }
        }
    }
    // Se valida el Total
    if(!isset($datos['factura']['Total']))
    {
        $res['log'][] = sprintf('ERROR: Falta campo factura.total');
    }
    else
    {
        // total = subtotal + traslados - retenciones - descuento
        $total = $datos['factura']['SubTotal'] + $res['impuestos_traslados'] - $datos['factura']['Descuento'];
        if($total != $datos['factura']['Total'])
        {
            $res['log'][] = sprintf('ERROR: El campo factura.total(%s) no corresponde con la operacion: total = subtotal(%s) + traslados(%s) - descuento(%s)',
                floatval($datos['factura']['Total']),
                floatval($datos['factura']['SubTotal']),
                floatval($res['impuestos_traslados']),
                floatval($datos['factura']['Descuento'])
            );
        }
    }

    if(isset($datos['version_cfdi']))
    {
        if($datos['version_cfdi'] != '3.3')
        {
            $res['log'][] = 'ERROR: Version diferente de 3.3';
        }
    }
    if(isset($datos['factura']['version']))
    {
        if($datos['factura']['version'] != '3.3')
        {
            $res['log'][] = 'ERROR: Version diferente de 3.3';
        }
    }
}

function cfdi2array($xml)
{
    // Arreglo con la informacion del CFDi
    $datos = array();

    // Se lee el CFDI
    $simplexml = simplexml_load_string($xml);

    // Se parsean los componentes del CFDI
    parse_comprobante($simplexml, $datos);
    parse_cfdirelacionados($simplexml, $datos);
    parse_emisor($simplexml, $datos);
    parse_receptor($simplexml, $datos);
    parse_conceptos($simplexml, $datos);
    parse_impuestos($simplexml, $datos);
    return $datos;

}

/**
 * @param $simplexml SimpleXMLElement
 * @param $datos array
 */
function parse_cfdirelacionados($simplexml, &$datos)
{
    $cfdis = array();


    parsea_nodo('CfdisRelacionados', '/cfdi:Comprobante/cfdi:CfdiRelacionados', $simplexml, $cfdis);

    $cfdis_rel = $simplexml->xpath('/cfdi:Comprobante/cfdi:CfdiRelacionados/*');
    if(count($cfdis_rel) > 0)
    {
        if(!isset($cfdis['CfdisRelacionados']))
        {
            $cfdis['CfdisRelacionados'] = array();
        }

        $uuids = array();
        for($i = 0; $i < count($cfdis_rel); $i++)
        {
            $cfdis['CfdisRelacionados']['UUID'][$i] = (string)$cfdis_rel[$i]['UUID'];
        }
    }
    $datos = array_merge($datos, $cfdis);
}

/**
 * @param $simplexml SimpleXMLElement
 * @param $datos array
 */
function parse_comprobante($simplexml, &$datos)
{
    parsea_nodo('factura', '/cfdi:Comprobante', $simplexml, $datos);
}

/**
 * @param $simplexml SimpleXMLElement
 * @param $datos array
 */
function parse_emisor($simplexml, &$datos)
{
    parsea_nodo('emisor', '/cfdi:Comprobante/cfdi:Emisor', $simplexml, $datos);
}

/**
 * @param $simplexml SimpleXMLElement
 * @param $datos array
 */
function parse_receptor($simplexml, &$datos)
{
    parsea_nodo('receptor', '/cfdi:Comprobante/cfdi:Receptor', $simplexml, $datos);
}

/**
 * @param $simplexml SimpleXMLElement
 * @param $datos array
 */
function parse_conceptos($simplexml, &$datos)
{
    $lista_conceptos = $simplexml->xpath('/cfdi:Comprobante/cfdi:Conceptos/*');

    for($c = 0; $c < count($lista_conceptos); $c++)
    {
        $concepto = $lista_conceptos[$c];

        // Se crea el arreglo $datos['conceptos']
        if(!isset($datos['conceptos']))
        {
            $datos['conceptos'] = array();
        }

        // Se agrega la informacion del concepto
        $datos['conceptos'] = array_merge($datos['conceptos'], nodo2array($c, $concepto));

        // Traslados
        $lista_traslados = $concepto->xpath('.//cfdi:Traslado');
        for($t = 0; $t < count($lista_traslados); $t++)
        {
            $traslado = $lista_traslados[$t];
            if(!isset($datos['conceptos'][$c]['Impuestos']['Traslados']))
            {
                $datos['conceptos'][$c]['Impuestos']['Traslados'] = array();
            }
            $datos['conceptos'][$c]['Impuestos']['Traslados'] = array_merge($datos['conceptos'][$c]['Impuestos']['Traslados'], nodo2array($t, $traslado));
        }

        // Retenciones
        $lista_retenciones = $concepto->xpath('.//cfdi:Retencion');
        for($r = 0; $r < count($lista_retenciones); $r++)
        {
            $retencion = $lista_retenciones[$r];
            if(!isset($datos['conceptos'][$c]['Impuestos']['Retenciones']))
            {
                $datos['conceptos'][$c]['Impuestos']['Retenciones'] = array();
            }
            $datos['conceptos'][$c]['Impuestos']['Retenciones'] = array_merge($datos['conceptos'][$c]['Impuestos']['Retenciones'], nodo2array($r, $retencion));
        }

        // Informacion aduanera
        $lista_infoaduanera = $concepto->xpath('.//cfdi:InformacionAduanera');
        for($ia = 0; $ia < count($lista_infoaduanera); $ia++)
        {
            $info_aduanera = $lista_infoaduanera[$ia];
            if(!isset($datos['conceptos'][$c]['InformacionAduanera']))
            {
                $datos['conceptos'][$c]['InformacionAduanera'] = array();
            }
            $datos['conceptos'][$c]['InformacionAduanera'] = array_merge($datos['conceptos'][$c]['InformacionAduanera'], nodo2array($ia, $info_aduanera));
        }

        // Cuenta Predial
        $lista_predial = $concepto->xpath('.//cfdi:CuentaPredial');
        if(count($lista_predial) > 0)
        {
            $datos['conceptos'][$c]['predial'] = (string)$lista_predial[0]['Numero'];
        }
    }
}

/**
 * @param $simplexml SimpleXMLElement
 * @param $datos array
 */
function parse_impuestos($simplexml, &$datos)
{
    $impuestos = $simplexml->xpath('/cfdi:Comprobante/cfdi:Impuestos');
    if(count($impuestos) > 0)
    {
        // Retenciones
        $lista_retenciones = $impuestos[0]->xpath('.//cfdi:Retencion');
        for($r = 0; $r < count($lista_retenciones); $r++)
        {
            $retencion = $lista_retenciones[$r];
            if(!isset($datos['impuestos']['retenciones']))
            {
                $datos['impuestos']['retenciones'] = array();
            }
            $datos['impuestos']['retenciones'] = array_merge($datos['impuestos']['retenciones'], nodo2array($r, $retencion));
        }

        // Traslados
        $lista_traslados = $impuestos[0]->xpath('.//cfdi:Traslado');
        for($t = 0; $t < count($lista_traslados); $t++)
        {
            $traslado = $lista_traslados[$t];
            if(!isset($datos['impuestos']['translados']))
            {
                $datos['impuestos']['translados'] = array();
            }
            $datos['impuestos']['translados'] = array_merge($datos['impuestos']['translados'], nodo2array($t, $traslado));
        }
    }
}

/**
 * @param $key mixed
 * @param $xpath string
 * @param $simplexml SimpleXMLElement
 * @param $array array
 */
function parsea_nodo($key, $xpath, $simplexml, &$array)
{
    $lista_nodos = $simplexml->xpath($xpath);
    if(count($lista_nodos) > 0)
    {
        $nodo = $lista_nodos[0];
        $array = array_merge($array, nodo2array($key, $nodo));
    }
}

/**
 * @param $key string
 * @param $nodo SimpleXMLElement
 * @return array
 */
function nodo2array($key, $nodo)
{
    $array = array();
    foreach($nodo->attributes() as $name => $value)
    {
        $array[(string)$name] = (string)$value;
    }
    return array($key => $array);
}