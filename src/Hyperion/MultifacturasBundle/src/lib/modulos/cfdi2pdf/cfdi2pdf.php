<?php

function ___cfdi2pdf($datos)
{
    // Respuesta del modulo
    $respuesta_modulo = array(
        'codigo_mf_numero' => 1,
        'codigo_mf_texto' => 'Error al generar el PDF'
    );

    // Se valida que exista el xml a convertir
    $pathinfo_xml = realpath($datos['rutaxml']);
    if(file_exists($pathinfo_xml) === false)
    {
        $respuesta_modulo['codigo_mf_numero'] = 2;
        $respuesta_modulo['codigo_mf_texto'] = 'No se encontro el archivo xml';
        return $respuesta_modulo;
    }

    // Se genera la el HTML de la plantilla
    $datos['modulo'] = 'cfdi2html';
    $res = mf_ejecuta_modulo($datos);
    if(isset($res['html']) === false)
    {
        $respuesta_modulo['codigo_mf_numero'] = 3;
        $respuesta_modulo['codigo_mf_texto'] = 'No se pudo generar la plantilla';
        return $respuesta_modulo;
    }
    else
    {
        // Se genera el PDF con la plantilla HTML
        $datos['html'] = $res['html'];
        $datos['modulo'] = 'html2pdf';
        $res = mf_ejecuta_modulo($datos);
        if($res == 'OK')
        {
            $respuesta_modulo['codigo_mf_numero'] = 0;
            $respuesta_modulo['codigo_mf_texto'] = 'OK';
        }
        else
        {
            $respuesta_modulo['codigo_mf_numero'] = 4;
            $respuesta_modulo['codigo_mf_texto'] = 'Error al guardar pdf';
        }
    }

    return $respuesta_modulo;
}