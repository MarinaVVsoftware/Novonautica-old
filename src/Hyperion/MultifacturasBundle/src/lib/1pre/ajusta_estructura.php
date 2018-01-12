<?php
function mf_ajusta_estructura($datos)
{
    global $__mf_constantes__;
    // Se ajusta el regimen
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.2')
    {
        if(isset($datos['factura']))
        {
            if(isset($datos['factura']['RegimenFiscal']))
            {
                $regimen = $datos['factura']['RegimenFiscal'];
                unset($datos['factura']['RegimenFiscal']);
                if(isset($datos['emisor']))
                {
                    $datos['emisor']['RegimenFiscal'] = array('Regimen' => $regimen);
                }
            }
        }
    }
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.3')
    {
        if(isset($datos['factura']))
        {
            if(isset($datos['factura']['RegimenFiscal']))
            {
                $regimen = $datos['factura']['RegimenFiscal'];
                unset($datos['factura']['RegimenFiscal']);
                if(isset($datos['emisor']))
                {
                    $datos['emisor']['RegimenFiscal'] = $regimen;
                }
            }
        }
    }
    return $datos;
}