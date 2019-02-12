<?php
/**
 * La funcion siempre debe comenzar con tres guiones bajo y el nombre del mismo archivo PHP
 * SIN extension, y recibir una variable; esta variable puede tener el nombre que se desee.
 */
function ___dof($datos)
{
    $url='http://dof.gob.mx/indicadores.php';
    $html= file_get_contents($url);
    list($tmp1,$tmp2)=explode('DOLAR</span> <br />',$html);
    list($dolar,$tmp2)=explode('</p>',$tmp2,2);
    echo $html;
    $datos['dolar']=$dolar;
    return $datos;
}