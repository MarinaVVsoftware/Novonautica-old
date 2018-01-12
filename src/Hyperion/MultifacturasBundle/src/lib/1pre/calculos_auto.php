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
        // Importe del concepto
        $importe_concepto = doubleval($concepto['importe']);
        $concepto['importe'] = mf_ajusta_decimales($concepto['importe']);
        $concepto['valorunitario'] = mf_ajusta_decimales($concepto['valorunitario']);
        $subtotal += $importe_concepto;

        // Se suman los descuentos
        $descuento += doubleval($concepto['Descuento']);

        // Traslados de Impuestos
        if(isset($concepto['Impuestos']['Traslados']))
        {
            foreach($concepto['Impuestos']['Traslados'] as &$traslado)
            {
                // Base del Impuesto
                $traslado['Base'] = mf_ajusta_decimales($importe_concepto);
                // Se calcula el importe del impuesto
                $traslado['Importe'] = mf_ajusta_decimales(doubleval($traslado['TasaOCuota']) * doubleval($traslado['Base']));

                // Suma total de impuestos
                $traslados += doubleval($traslado['Importe']);
                // Desgloce de impuestos
                $sum_imp_tas['t'][$traslado['Impuesto']][$traslado['TipoFactor']][$traslado['TasaOCuota']] = mf_ajusta_decimales(doubleval($sum_imp_tas['t'][$traslado['Impuesto']][$traslado['TipoFactor']][$traslado['TasaOCuota']]) + doubleval($traslado['Importe']));
            }
        }

        // Retenciones de Impuestos
        if(isset($concepto['Impuestos']['Retenciones']))
        {
            foreach($concepto['Impuestos']['Retenciones'] as &$retencion)
            {
                // Base del Impuesto
                $retencion['Base'] = mf_ajusta_decimales($importe_concepto);
                // Se calcula el importe del impuesto
                $retencion['Importe'] = mf_ajusta_decimales(doubleval($retencion['TasaOCuota']) * doubleval($retencion['Base']));

                // Suma total de impuestos
                $retenciones += doubleval($retencion['Importe']);
                // Desgloce de impuestos
                $sum_imp_tas['r'][$retencion['Impuesto']][$retencion['TipoFactor']][$retencion['TasaOCuota']] = mf_ajusta_decimales(doubleval($sum_imp_tas['r'][$retencion['Impuesto']][$retencion['TipoFactor']][$retencion['TasaOCuota']]) + doubleval($retencion['Importe']));
            }
        }
    }

    // Se asignan los campos
    $datos['factura']['subtotal'] = mf_ajusta_decimales($subtotal);
    $datos['factura']['descuento'] = mf_ajusta_decimales($descuento);
    $datos['factura']['total'] = mf_ajusta_decimales(($subtotal - $descuento) - $retenciones + $traslados);




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
                        'Importe' => mf_ajusta_decimales($importe)
                    );
                }
            }
        }
        // Debe estar aqui, si no existe el nodo traslados se omite
        $datos['impuestos']['TotalImpuestosTrasladados'] = mf_ajusta_decimales($traslados);
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
                        'Importe' => mf_ajusta_decimales($importe)
                    );
                }
            }
        }
        // Debe estar aqui, si no existe el nodo retenciones se omite
        $datos['impuestos']['TotalImpuestosRetenidos'] = mf_ajusta_decimales($retenciones);
    }
}