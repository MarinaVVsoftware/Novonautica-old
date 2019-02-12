<?php
function mf_nodo_cfdisrelacionados(array $datos)
{
    $atrs = mf_atributos_nodo($datos, '');
    $xml = "<cfdi:CfdiRelacionados $atrs>";
    if(isset($datos['UUID']))
    {
        foreach ($datos['UUID'] as $idx => $uuid)
        {
            $xml .= "<cfdi:CfdiRelacionado UUID='$uuid'/>";
        }
    }
    $xml.="</cfdi:CfdiRelacionados>";
    return $xml;
}