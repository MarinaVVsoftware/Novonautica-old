<?php

/**
 * La funcion siempre debe comenzar con tres guiones bajo y el nombre del mismo archivo PHP
 * SIN extension, y recibir una variable; esta variable puede tener el nombre que se desee.
 */
function ___codigopostal($datos)
{
    // Se extraen los datos necesarios del parametro de entrada
    $cp=$datos['CP'];
    
    // Se procesa la informacion recibida y/o ejecutan las operaciones pertinentes
    $cp=intval($cp);
    $url="http://facturacion.mashter.com/cp/cp.php?cp=$cp";
    $txt= file_get_contents($url);
    
    /*
     * Siempre se debe de regresar un arreglo asociativo, es decir que los datos que se
     * pretendan devolver siempre se puedan identificar/localizar por medio de una cadena
     */
    return array('resultado' => $txt);
}