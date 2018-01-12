<?php

function mf_remueve_acentos($datos)
{
    if(isset($datos['remueve_acentos']) && $datos['remueve_acentos']=='SI')
    {
        //$datos = array_map_recursive('cfd_fix_dato_xml_acentos', $datos);
    }
    return $datos;
}