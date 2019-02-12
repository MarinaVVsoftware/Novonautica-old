<?php

function ___qr($datos)
{
    $cadenaqr=$datos['cadena'];
    $archivo_png=$datos['archivo_png'];
    include('qrcode/qrlib.php');
    $fileName = $archivo_png;
    QRcode::png($cadenaqr, $fileName, QR_ECLEVEL_L, 4);
    $base64= base64_encode(file_get_contents($archivo_png));
    
    $res['cadena']=$cadenaqr;
    $res['archivo_png']=$archivo_png;
    $res['archivo_base64']=$base64;
    return $res;    
}
     
    
?>