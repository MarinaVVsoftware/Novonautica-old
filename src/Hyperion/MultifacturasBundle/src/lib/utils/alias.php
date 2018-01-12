<?php

function mv_version_alias()
{
    return 1.0;
}

function mf_init_util_alias()
{
    // Globales a usar
    global $__mf_constantes__;

    // Se valida la version de CFDi
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.3')
    {
        // Alias
        $__mf_alias33__['factura']['version'] = 'Version';
        $__mf_alias33__['factura']['serie'] = 'Serie';
        $__mf_alias33__['factura']['folio'] = 'Folio';
        $__mf_alias33__['factura']['fecha_expedicion'] = 'Fecha';
        $__mf_alias33__['factura']['metodo_pago'] = 'MetodoPago';
        $__mf_alias33__['factura']['forma_pago'] = 'FormaPago';
        $__mf_alias33__['factura']['tipocomprobante'] = 'TipoDeComprobante';
        $__mf_alias33__['factura']['moneda'] = 'Moneda';
        $__mf_alias33__['factura']['tipocambio'] = 'TipoCambio';
        $__mf_alias33__['factura']['subtotal'] = 'SubTotal';
        $__mf_alias33__['factura']['total'] = 'Total';
        $__mf_alias33__['factura']['descuento'] = 'Descuento';
        $__mf_alias33__['factura']['noCertificado'] = 'NoCertificado';
        $__mf_alias33__['factura']['certificado'] = 'Certificado';
        $__mf_alias33__['factura']['sello'] = 'Sello';
        $__mf_alias33__['factura']['condicionesDePago'] = 'CondicionesDePago';

        $__mf_alias33__['emisor']['rfc'] = 'Rfc';
        $__mf_alias33__['emisor']['nombre'] = 'Nombre';

        $__mf_alias33__['receptor']['rfc'] = 'Rfc';
        $__mf_alias33__['receptor']['nombre'] = 'Nombre';

        $__mf_alias33__['conceptos']['ID'] = 'NoIdentificacion';
        $__mf_alias33__['conceptos']['valorunitario'] = 'ValorUnitario';
        $__mf_alias33__['conceptos']['cantidad'] = 'Cantidad';
        $__mf_alias33__['conceptos']['unidad'] = 'Unidad';
        $__mf_alias33__['conceptos']['descripcion'] = 'Descripcion';
        $__mf_alias33__['conceptos']['importe'] = 'Importe';

        $__mf_alias33__['impuestos']['translados']['importe'] = 'Importe';
        $__mf_alias33__['impuestos']['translados']['tasa'] = 'TasaOCuota';
        $__mf_alias33__['impuestos']['translados']['impuesto'] = 'Impuesto';

        $__mf_alias33__['impuestos']['retenciones']['impuesto'] = 'Impuesto';
        $__mf_alias33__['impuestos']['retenciones']['importe'] = 'Importe';
        mf_agrega_alias($__mf_alias33__);
    }
    else
    {
        // Alias para CFDi 3.2
        $__mf_alias32__['factura']['serie'] = 'serie';
        $__mf_alias32__['factura']['folio'] = 'folio';
        $__mf_alias32__['factura']['fecha_expedicion'] = 'fecha';
        $__mf_alias32__['factura']['metodo_pago'] = 'metodoDePago';
        $__mf_alias32__['factura']['forma_pago'] = 'formaDePago';
        $__mf_alias32__['factura']['tipocomprobante'] = 'tipoDeComprobante';
        $__mf_alias32__['factura']['moneda'] = 'Moneda';
        $__mf_alias32__['factura']['tipocambio'] = 'TipoCambio';
        //$__mf_alias32__['factura']['descuento'] = 'motivoDescuento';
        $__mf_alias32__['factura']['subtotal'] = 'subTotal';

        $__mf_alias32__['emisor']['DomicilioFiscal']['CodigoPostal'] = 'codigoPostal';
        $__mf_alias32__['emisor']['ExpedidoEn']['CodigoPostal'] = 'codigoPostal';

        $__mf_alias32__['receptor']['Domicilio']['CodigoPostal'] = 'codigoPostal';

        $__mf_alias32__['conceptos']['ID'] = 'noIdentificacion';
        $__mf_alias32__['conceptos']['valorunitario'] = 'valorUnitario';
        mf_agrega_alias($__mf_alias32__);
    }
}

/**
 * Busca el alias para un campo
 * @param $campo
 * @return string
 */
function mf_busca_alias($campo)
{
    global $__mf_alias__;
    $arregloAlias = '$__mf_alias__';
    $rutaArreglo = explode('.', $campo);
    $xpath = "$arregloAlias";
    $alias = '';
    for($i = 0; $i < count($rutaArreglo); $i++)
    {
        $cmpo = $rutaArreglo[$i];
        $xpath .= "['$cmpo']";
        $alias = $cmpo;
    }
    $code = "\$alias = (isset($xpath)) ? $xpath : \$alias;";
    eval($code);
    return $alias;
}

/**
 * Agrega una lista de alias
 * @param array $alias
 */
function mf_agrega_alias(array $alias)
{
    global $__mf_alias__;
    $__mf_alias__ = array_merge($__mf_alias__, $alias);
}