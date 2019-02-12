<?php

function ___timbrarini($datos)
{
    global $__mf_constantes__;
    mf_carga_libreria($__mf_constantes__['__MF_LIBS_DIR__'] . 'nusoap/nusoap.php');

    $pac = rand(1, 10);
    $url_ws = "http://ini.facturacionmexico.com.mx/pac/timbrarini.php?wsdl ";
    //$url_ws = "http://192.168.10.111/pac/timbrarini.php?wsdl";
    $cliente = new nusoap_client($url_ws);

    unset($datos['modulo']);

    $inib64 = arr2ini($datos);
    $inib64 = base64_encode($inib64);

    $cer = $datos['conf']['cer'];
    $cer = strtoupper($cer);
    $fin_cer = substr($cer, -4);

    if($fin_cer == '.PEM')
    {
        $cer = substr($datos['conf']['cer'], 0, strlen($cer) - 4);
    }
    $key = $datos['conf']['key'];
    $key = strtoupper($key);
    $fin_key = substr($key, -4);

    if($fin_key == '.PEM')
    {
        $key = substr($datos['conf']['key'], 0, strlen($key) - 4);
    }

    $params = array(
        'rfc' => $datos['emisor']['rfc'], //$datos['PAC']['usuario'],
        'inib64' => $inib64,
        'cer' => base64_encode(file_get_contents($cer)),
        'key' => base64_encode(file_get_contents($key)),
        'pass' => $datos['conf']['pass']
    );

    $resp = $cliente->call('timbrarini2', $params);

    return $resp;
}