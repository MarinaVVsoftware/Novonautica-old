<?php

function mf_calculos_auto(&$datos)
{
    // Busca los modulos de calculos automaticos para complementos
    if(isset($datos['complemento']))
    {
        $complemento = $datos['complemento'];
        $ruta_modulo = __DIR__ . "/calculos_auto_$complemento.php";
        if(file_exists($ruta_modulo))
        {
            include_once $ruta_modulo;
            eval("mf_calculos_auto_$complemento(\$datos);");
        }
    }

    // Variables a utilizar
    $sum_imp_tas = array();
    $traslados = 0;
    $retenciones = 0;
    $subtotal = 0;
    $descuento = 0;

    // Se calculan los impuestos
    foreach($datos['conceptos'] as $idx => &$concepto)
    {
        // Cantidad del producto
        //$cantidad = mf_truncar($concepto['cantidad']);
        $cantidad = mf_redondear($concepto['cantidad']);
        $concepto['cantidad'] = $cantidad;

        // Valor Unitario
        //$valor_unitario = mf_truncar($concepto['valorunitario']);
        $valor_unitario = mf_redondear($concepto['valorunitario']);
        $concepto['valorunitario'] = $valor_unitario;

        // Importe del concepto
        //$importe_concepto = mf_truncar($cantidad * $valor_unitario);
        $importe_concepto = mf_redondear($cantidad * $valor_unitario);
        $concepto['importe'] = $importe_concepto;
        $subtotal += $importe_concepto;

        // Se suman los descuentos
        $descuento += doubleval($concepto['Descuento']);

        // Traslados de Impuestos
        if(isset($concepto['Impuestos']['Traslados']))
        {
            foreach($concepto['Impuestos']['Traslados'] as &$traslado)
            {
                // Base del Impuesto
                //$traslado['Base'] = mf_truncar($importe_concepto);
                $traslado['Base'] = mf_redondear($importe_concepto);
                // Se calcula el importe del impuesto
                //$traslado['Importe'] = mf_truncar(doubleval($traslado['TasaOCuota']) * doubleval($traslado['Base']));
                $traslado['Importe'] = mf_redondear(doubleval($traslado['TasaOCuota']) * doubleval($traslado['Base']));

                // Suma total de impuestos
                $traslados += doubleval($traslado['Importe']);
                // Desgloce de impuestos
                //$sum_imp_tas['t'][$traslado['Impuesto']][$traslado['TipoFactor']][$traslado['TasaOCuota']] = mf_truncar(doubleval($sum_imp_tas['t'][$traslado['Impuesto']][$traslado['TipoFactor']][$traslado['TasaOCuota']]) + doubleval($traslado['Importe']));
                $sum_imp_tas['t'][$traslado['Impuesto']][$traslado['TipoFactor']][$traslado['TasaOCuota']] = mf_redondear(doubleval($sum_imp_tas['t'][$traslado['Impuesto']][$traslado['TipoFactor']][$traslado['TasaOCuota']]) + doubleval($traslado['Importe']));
            }
        }

        // Retenciones de Impuestos
        if(isset($concepto['Impuestos']['Retenciones']))
        {
            foreach($concepto['Impuestos']['Retenciones'] as &$retencion)
            {
                // Base del Impuesto
                //$retencion['Base'] = mf_truncar($importe_concepto);
                $retencion['Base'] = mf_redondear($importe_concepto);
                // Se calcula el importe del impuesto
                //$retencion['Importe'] = mf_truncar(doubleval($retencion['TasaOCuota']) * doubleval($retencion['Base']));
                $retencion['Importe'] = mf_redondear(doubleval($retencion['TasaOCuota']) * doubleval($retencion['Base']));

                // Suma total de impuestos
                $retenciones += doubleval($retencion['Importe']);
                // Desgloce de impuestos
                //$sum_imp_tas['r'][$retencion['Impuesto']][$retencion['TipoFactor']][$retencion['TasaOCuota']] = mf_truncar(doubleval($sum_imp_tas['r'][$retencion['Impuesto']][$retencion['TipoFactor']][$retencion['TasaOCuota']]) + doubleval($retencion['Importe']));
                $sum_imp_tas['r'][$retencion['Impuesto']][$retencion['TipoFactor']][$retencion['TasaOCuota']] = mf_redondear(doubleval($sum_imp_tas['r'][$retencion['Impuesto']][$retencion['TipoFactor']][$retencion['TasaOCuota']]) + doubleval($retencion['Importe']));
            }
        }
    }

    // Se asignan los campos
    //$datos['factura']['subtotal'] = mf_truncar($subtotal);
    $datos['factura']['subtotal'] = mf_redondear($subtotal);

	//$descuento_global = mf_truncar($descuento);
	$descuento_global = mf_redondear($descuento);
	if($descuento_global != 0)
	{
		$datos['factura']['descuento'] = $descuento_global;
	}
    //$datos['factura']['total'] = mf_truncar(($subtotal - $descuento) - $retenciones + $traslados);
    $datos['factura']['total'] = mf_redondear(($subtotal - $descuento) - $retenciones + $traslados);

    // Se agregan los impuestos trasladados
    if(isset($sum_imp_tas['t']))
    {
        foreach($sum_imp_tas['t'] as $impuesto => $datos_impuestos)
        {
            foreach($datos_impuestos as $tipofactor => $datos_tipofactor)
            {
                foreach($datos_tipofactor as $tasacuota => $importe)
                {
                    $datos['impuestos']['translados'][] = array(
                        'Impuesto' => $impuesto,
                        'TipoFactor' => $tipofactor,
                        'TasaOCuota' => $tasacuota,
                        //'Importe' => mf_truncar($importe)
                        'Importe' => mf_redondear($importe)
                    );
                }
            }
        }
        // Debe estar aqui, si no existe el nodo traslados se omite
        //$datos['impuestos']['TotalImpuestosTrasladados'] = mf_truncar($traslados);
        $datos['impuestos']['TotalImpuestosTrasladados'] = mf_redondear($traslados);
    }

    // Se agregan los impuestos retenidos
    if(isset($sum_imp_tas['r']))
    {
        foreach($sum_imp_tas['r'] as $impuesto => $datos_impuestos)
        {
            foreach($datos_impuestos as $tipofactor => $datos_tipofactor)
            {
                foreach($datos_tipofactor as $tasacuota => $importe)
                {
                    $datos['impuestos']['retenciones'][] = array(
                        'Impuesto' => $impuesto,
                        'TipoFactor' => $tipofactor,
                        'TasaOCuota' => $tasacuota,
                        //'Importe' => mf_truncar($importe)
                        'Importe' => mf_redondear($importe)
                    );
                }
            }
        }
        // Debe estar aqui, si no existe el nodo retenciones se omite
        //$datos['impuestos']['TotalImpuestosRetenidos'] = mf_truncar($retenciones);
        $datos['impuestos']['TotalImpuestosRetenidos'] = mf_redondear($retenciones);
    }
}

function mf_truncar($valor, $decimales=2)
{
    $factor = pow(10, $decimales);
    $aux = intval($valor * $factor);
    return mf_ajusta_decimales(doubleval($aux / doubleval($factor)));
}

function mf_redondear($valor, $decimales=2)
{
    return round($valor, $decimales);
}