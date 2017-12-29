<?php

function mf_calculos_auto_nomina12(&$datos)
{
    // Percepciones
    $totalPercepciones = 0.0;
    $totalPercepcionesExentas = 0.0;
    $totalPercepcionesGravadas = 0.0;
    $totalSueldos = 0.0;
    $totalJubilacionPensionRetiro = 0.0;
    $totalSeparacionIndemnizacion = 0.0;

    // Deducciones
    $totalOtrasDeducciones = 0.0;
    $totalImpuestosRetenidos = 0.0;

    // Otros Pagos
    $totalOtrosPagos = 0.0;

    // Tipos de perseccion en los que NO se suma a total sueldos
    $casos_noSuma_TotalSueldo = array('022', '023', '025', '039', '044');
    // Tipos de percepcion en los que SI se suma totaljubilacionpensionretiro
    $casos_jubilacion = array('039', '044');
    // Tipos de percepcion en los que SI se suma totalseparacion
    $casos_separacion = array('022', '023', '025');

    // No debe existir el nodo impuestos
    //unset($datos['impuestos']);
    //unset($datos['factura']['condicionesDePago']);

    // Se suman las percepciones
    if(isset($datos['nomina12']['Percepciones']))
    {
        foreach($datos['nomina12']['Percepciones'] as $idx => &$percepcion)
        {
            // Percepciones
            if(is_integer($idx))
            {
                if(isset($percepcion['ImporteExento']))
                {
                    $totalPercepcionesExentas += $percepcion['ImporteExento'];
                }
                if(isset($percepcion['ImporteGravado']))
                {
                    $totalPercepcionesGravadas += $percepcion['ImporteGravado'];
                }
                // Percepciones normales
                if(array_search($percepcion['TipoPercepcion'], $casos_noSuma_TotalSueldo) === false)
                {
                    $totalSueldos += ($percepcion['ImporteExento'] + $percepcion['ImporteGravado']);
                }
                // JubilacionPensionRetiro
                elseif(array_search($percepcion['TipoPercepcion'], $casos_jubilacion) === true){
                    $totalJubilacionPensionRetiro += ($percepcion['ImporteExento'] + $percepcion['ImporteGravado']);
                }
                // SeparacionIndeminizacion
                elseif(array_search($percepcion['TipoPercepcion'], $casos_separacion) === true){
                    $totalSeparacionIndemnizacion += ($percepcion['ImporteExento'] + $percepcion['ImporteGravado']);
                }
            }
            else
            {
                switch($idx)
                {
                    case 'JubilacionPensionRetiro':
                        $datos['nomina12']['Percepciones']['TotalJubilacionPensionRetiro'] = $totalJubilacionPensionRetiro;
                        break;
                    case 'SeparacionIndemnizacion':
                        $datos['nomina12']['Percepciones']['TotalSeparacionIndemnizacion'] = $totalSeparacionIndemnizacion;
                        break;
                }
            }
        }

        // Totales de Percepciones
        $datos['nomina12']['Percepciones']['TotalSueldos'] = $totalSueldos;
        $datos['nomina12']['Percepciones']['TotalGravado'] = $totalPercepcionesGravadas;
        $datos['nomina12']['Percepciones']['TotalExento'] = $totalPercepcionesExentas;

        // Total percepciones
        $datos['nomina12']['TotalPercepciones'] = $datos['nomina12']['Percepciones']['TotalSueldos'] +
            $datos['nomina12']['Percepciones']['TotalSeparacionIndemnización'] +
            $datos['nomina12']['Percepciones']['TotalJubilaciónPensiónRetiro)'];
    }

    // Deducciones
    if(isset($datos['nomina12']['Deducciones']))
    {
        foreach($datos['nomina12']['Deducciones'] as $idx => $deduccion)
        {
            if($deduccion['TipoDeduccion'] == '002')
            {
                $totalImpuestosRetenidos += $deduccion['Importe'];
            }
            else
            {
                $totalOtrasDeducciones += $deduccion['Importe'];
            }
        }

        // Totales Deducciones
        $datos['nomina12']['Deducciones']['TotalImpuestosRetenidos'] = $totalImpuestosRetenidos;
        $datos['nomina12']['Deducciones']['TotalOtrasDeducciones'] = $totalOtrasDeducciones;

        // Total Deducciones
        $datos['nomina12']['TotalDeducciones'] = $totalImpuestosRetenidos + $totalOtrasDeducciones;
    }

    // Otros Pagos
    if(isset($datos['nomina12']['OtrosPagos']))
    {
        foreach($datos['nomina12']['OtrosPagos'] as $idx => $otropago)
        {
            $totalOtrosPagos += $otropago['Importe'];
        }
        $datos['nomina12']['TotalOtrosPagos'] = $totalOtrosPagos;
    }

    // Incapacidades
    if(isset($datos['nomina12']['Incapacidades']))
    {

    }

    // Se establece el tipo nomina
    $datos['factura']['tipocomprobante'] = 'N';
    $datos['factura']['forma_pago'] = '99';

    // Se pone por defecto el concepto de pago de nomina
    $valorunitario = $datos['nomina12']['TotalPercepciones'] + $datos['nomina12']['TotalOtrosPagos'];
    $importe = $valorunitario;
    $descuento = $datos['nomina12']['TotalDeducciones'];
    $datos['conceptos'] = array(
        0 => array(
            'cantidad' => '1',
            'ClaveUnidad' => 'ACT',
            'ClaveProdServ' => '84111505',
            'descripcion' => 'Pago de nómina',
            'valorunitario' => $valorunitario,
            'importe' => $importe,
            'Descuento' => $datos['nomina12']['TotalDeducciones']
        )
    );
}