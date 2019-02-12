<?php

function mf_nodo_emisor($datos)
{
    global $__mf_constantes__;
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.2')
    {
        $atributos = mf_atributos_nodo($datos, 'emisor');
        $subnodos = '';
        if(isset($datos['DomicilioFiscal']))
        {
            $subatrs = mf_atributos_nodo($datos['DomicilioFiscal'], 'emisor.DomicilioFiscal');
            $sub = "<cfdi:DomicilioFiscal $subatrs/>";
            $subnodos.=$sub;
        }
        if(isset($datos['ExpedidoEn']))
        {
            $subatrs = mf_atributos_nodo($datos['ExpedidoEn'], 'emisor.ExpedidoEn');
            $sub = "<cfdi:ExpedidoEn $subatrs/>";
            $subnodos.=$sub;
        }
        if(isset($datos['RegimenFiscal']))
        {
            $subatrs = mf_atributos_nodo($datos['RegimenFiscal'], 'emisor.RegimenFiscal');
            $sub = "<cfdi:RegimenFiscal $subatrs/>";
            $subnodos.=$sub;
        }
        $emisor = "<cfdi:Emisor $atributos>$subnodos</cfdi:Emisor>";
    }
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.3')
    {
        $atributos = mf_atributos_nodo($datos, 'emisor');
        $emisor = "<cfdi:Emisor $atributos/>";
    }
    return $emisor;
}