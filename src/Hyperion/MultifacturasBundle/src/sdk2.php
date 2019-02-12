<?php

error_reporting(~(E_WARNING | E_NOTICE));

function mf_phpversion()
{
    $version = phpversion();
    $numversion = '';
    for ($i = 0, $punto = 0; $i < strlen($version); $i++) {
        if ($version[$i] == '.') {
            if ($punto == 0) {
                $numversion .= $version[$i];
                $punto++;
            }
        } else {
            $numversion .= $version[$i];
        }
    }

    $version_final = "$numversion";

    $ver = $version_final[0].'.'.$version_final[2];

    return $ver;
    //return doubleval($numversion);
}

///////////////////////////////////////////////////////
//phpinfo();

function mf_postfijo_php()
{
    $ruta = __DIR__.'/';

    $ruta = str_replace('\\', '/', $ruta);

    if ($_SERVER["SERVER_NAME"] == '55.cfdi.red' || $_SERVER['SERVER_ADDR'] == '192.168.10.11') {
        if (file_exists($ruta.'sdk2XX.php') == true) {
            return 'XX';
        } else {
            return '55';
        }

    }
    $php_version = mf_phpversion();

    switch ($php_version) {
        case 5.3:
            return '53';
            break;
        case 5.4:
            return '54';
            break;
        case 5.5:
            return '55';
            break;
        case 5.6:
            return '56';
            break;
        case 7.0:
            return '56';
            break;
        case 7.1:
            return '71';
            break;
        case 7.2:
            return '72';
            break;
        default :
            echo "La version '$php_version' no es compatible";
            die();
    }

}

////////////////////////////////////////////////
$php_version = mf_postfijo_php();
$ruta = __DIR__.'/';

$ruta = $ruta."sdk2$php_version.php";
require_once $ruta;


