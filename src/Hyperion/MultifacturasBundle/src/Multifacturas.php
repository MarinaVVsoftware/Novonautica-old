<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/9/18
 * Time: 12:35
 */

namespace Hyperion\MultifacturasBundle\src;

use AppBundle\Entity\Contabilidad\Facturacion;

error_reporting(~(E_WARNING | E_NOTICE));

class Multifacturas
{
    private $env;
    private $dir;
    private $config;

    public function __construct($env, $dir)
    {
        $this->env = $env;
        $this->dir = $dir;
        $this->config = $this->setConfig();
    }

    public function procesa(Facturacion $factura)
    {
        require_once 'sdk2.php';
        $datos = [];

        /*
         * Factura
         */
        $datos['factura']['condicionesDePago'] = 'CONDICIONES'; // EX: '3 Meses'
        $datos['factura']['descuento'] = ($factura->getDescuento() / 100);
        $datos['factura']['fecha_expedicion'] = $factura->getFecha()->format('Y-m-d\TH:i:s');
        $datos['factura']['folio'] = $factura->getFolioCotizacion();
        $datos['factura']['forma_pago'] = $factura->getFormaPago();
        $datos['factura']['LugarExpedicion'] = $factura->getEmisor()->getCodigoPostal();
        $datos['factura']['metodo_pago'] = $factura->getMetodoPago();
        $datos['factura']['moneda'] = 'USD'; // Definido por defecto?
        $datos['factura']['serie'] = ''; // ?? Número utilizado para control interno de su información, LENGTH = 1-25
        $datos['factura']['subtotal'] = ($factura->getSubtotal() / 100);
        $datos['factura']['tipocambio'] = ($factura->getTipoCambio() / 100);
        $datos['factura']['tipocomprobante'] = $factura->getTipoComprobante();
        $datos['factura']['total'] = ($factura->getTotal() / 100);
        $datos['factura']['RegimenFiscal'] = $factura->getEmisor()->getRegimenFiscal();

        /*
         * Emisor
         */
        $datos['emisor']['rfc'] = $factura->getEmisor()->getRfc();
        $datos['emisor']['nombre'] = $factura->getEmisor()->getNombre();
        $datos['conf']['cer'] = __DIR__ . '/certificados/' . $factura->getEmisor()->getCer();
        $datos['conf']['key'] = __DIR__ . '/certificados/' . $factura->getEmisor()->getKey();
        $datos['conf']['pass'] = $factura->getEmisor()->getPassword();

        /*
         * Receptor
         */
        $datos['receptor']['rfc'] = $factura->getRfc();
        $datos['receptor']['nombre'] = $factura->getRazonSocial(); // Este valor es muy ambiguo
        $datos['receptor']['UsoCFDI'] = 'G03'; // Este valor deberia ser elegible en el formulario?

        /**
         * Conceptos
         *
         * @var Facturacion\Concepto $concepto
         */
        foreach ($factura->getConceptos() as $i => $concepto) {
            $datos['conceptos'][$i]['cantidad'] = $concepto->getCantidad();
            $datos['conceptos'][$i]['unidad'] = $concepto->getUnidad();
            $datos['conceptos'][$i]['ID'] = $i;
            $datos['conceptos'][$i]['Descuento'] = ($concepto->getDescuento() / 100);
            $datos['conceptos'][$i]['descripcion'] = $concepto->getDescripcion();
            $datos['conceptos'][$i]['valorunitario'] = ($concepto->getValorunitario() / 100);
            $datos['conceptos'][$i]['importe'] = ($concepto->getSubtotal() / 100);
            $datos['conceptos'][$i]['ClaveProdServ'] = $concepto->getClaveProdServ();
            $datos['conceptos'][$i]['ClaveUnidad'] = $concepto->getClaveUnidad();


            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Base'] = ($concepto->getSubtotal() / 100);
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Impuesto'] = '002'; // Se refiere a iva
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['TipoFactor'] = 'Tasa'; // Tasa - Cuota - Exento?
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['TasaOCuota'] = '0.160000'; // Este valor sera fijo?
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Importe'] = ($concepto->getIva() / 100);
        }

        /*
         * Impuestos
         */
        $datos['impuestos']['translados'][0]['impuesto'] = '002'; // 002 se refiere a IVA
        $datos['impuestos']['translados'][0]['tasa'] = '0.160000'; // Este valor sera fijo?
        $datos['impuestos']['translados'][0]['importe'] = ($factura->getIva() / 100);
        $datos['impuestos']['translados'][0]['TipoFactor'] = 'Tasa'; // Tasa - Cuota - Exento?
        $datos['impuestos']['TotalImpuestosTrasladados'] = ($factura->getIva() / 100);

        /**
         * Ubicacion del timbrado
         */
        $datos['cfdi'] = $this->dir . '/web/timbrados/factura_' . $factura->getFolioCotizacion() . '.xml';
        $datos['xml_debug'] = $this->dir . '/web/timbrados/factura_' . $factura->getFolioCotizacion() . '_sintimbrar.xml';

        $cotizacion = array_merge($this->config, $datos);

        return mf_genera_cfdi($cotizacion);
    }

    private function setConfig(): array
    {
        $config = [];

        if ($this->env === 'dev') {
            $config['PAC']['usuario'] = 'DEMO700101XXX';
            $config['PAC']['pass'] = 'DEMO700101XXX';
            $config['PAC']['produccion'] = 'NO';
        }

        $config['version_cfdi'] = '3.3';
        return $config;
    }
}