<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/9/18
 * Time: 12:35
 */

namespace Hyperion\MultifacturasBundle\src;

use AppBundle\Entity\Contabilidad\Facturacion;

class Multifacturas
{
    private $env;
    private $dir;
    private $prod;

    public function __construct($env, $dir)
    {
        $this->env = $env;
        $this->dir = $dir;
        $this->prod = 'NO';
    }

    public function procesa(Facturacion $factura)
    {
        require_once 'sdk2.php';
        $datos = [];

        /*
         * PAC
         */
        $datos['version_cfdi'] = '3.3';
        $datos['PAC']['produccion'] = $this->prod;
        $datos['PAC']['usuario'] = $factura->getEmisor()->getUsuarioPAC();
        $datos['PAC']['pass'] = $factura->getEmisor()->getPasswordPAC();


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
        $datos['receptor']['nombre'] = $factura->getRazonSocial();
        $datos['receptor']['UsoCFDI'] = $factura->getUsoCFDI();

        if ($factura->getTipoComprobante() === 'P') {
            /*
             * Factura
             */
            unset($datos['factura']['condicionesDePago']);
            unset($datos['factura']['descuento']);
            $datos['factura']['fecha_expedicion'] = $factura->getFecha()->format('Y-m-d\TH:i:s');
            $datos['factura']['folio'] = $factura->getFolioCotizacion();
            unset($datos['factura']['forma_pago']);
            $datos['factura']['LugarExpedicion'] = $factura->getEmisor()->getCodigoPostal();
            unset($datos['factura']['metodo_pago']);
            $datos['factura']['moneda'] = $factura->getMoneda();
            $datos['factura']['serie'] = ''; // ?? Número utilizado para control interno de su información, LENGTH = 1-25
            $datos['factura']['tipocambio'] = ($factura->getTipoCambio() / 100);
            $datos['factura']['subtotal'] = ($factura->getSubtotal() / 100);
            $datos['factura']['total'] = ($factura->getTotal() / 100);
            $datos['factura']['tipocomprobante'] = $factura->getTipoComprobante();
            $datos['factura']['RegimenFiscal'] = $factura->getEmisor()->getRegimenFiscal();

            /**
             * Conceptos
             * @var Facturacion\Concepto $concepto
             */
            foreach ($factura->getConceptos() as $i => $concepto) {
                $datos['conceptos'][$i]['cantidad'] = $concepto->getCantidad();
                $datos['conceptos'][$i]['ID'] = $i;
                unset($datos['conceptos'][$i]['Descuento']);
                $datos['conceptos'][$i]['descripcion'] = $concepto->getDescripcion();
                $datos['conceptos'][$i]['valorunitario'] = ($concepto->getValorunitario() / 100);
                $datos['conceptos'][$i]['importe'] = ($concepto->getSubtotal() / 100);
                $datos['conceptos'][$i]['ClaveProdServ'] = $concepto->getClaveProdServ()->getClaveProdServ();
                $datos['conceptos'][$i]['ClaveUnidad'] = $concepto->getClaveUnidad()->getClaveUnidad();


                unset($datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Base']);
                unset($datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Impuesto']);
                unset($datos['conceptos'][$i]['Impuestos']['Traslados'][0]['TipoFactor']);
                unset($datos['conceptos'][$i]['Impuestos']['Traslados'][0]['TasaOCuota']);
                unset($datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Importe']);
            }

            /*
             * Impuestos
             */
            unset($datos['impuestos']['translados'][0]['impuesto']);
            unset($datos['impuestos']['translados'][0]['tasa']);
            unset($datos['impuestos']['translados'][0]['importe']);
            unset($datos['impuestos']['translados'][0]['TipoFactor']);
            unset($datos['impuestos']['TotalImpuestosTrasladados']);
        }
        else {
            /*
             * Factura
             * */
            $datos['factura']['condicionesDePago'] = $factura->getCondicionesPago(); // EX: '3 Meses'
            $datos['factura']['descuento'] = ($factura->getDescuento() / 100);
            $datos['factura']['fecha_expedicion'] = $factura->getFecha()->format('Y-m-d\TH:i:s');
            $datos['factura']['folio'] = $factura->getFolioCotizacion();
            $datos['factura']['forma_pago'] = $factura->getFormaPago();
            $datos['factura']['LugarExpedicion'] = $factura->getEmisor()->getCodigoPostal();
            $datos['factura']['metodo_pago'] = $factura->getMetodoPago();
            $datos['factura']['moneda'] = $factura->getMoneda();
            $datos['factura']['tipocambio'] = $factura->getMoneda() === 'MXN' ? 1.00 : ($factura->getTipoCambio() / 100);
//        $datos['factura']['serie'] = ''; // No requerido
            $datos['factura']['subtotal'] = ($factura->getSubtotal() / 100);
            $datos['factura']['tipocomprobante'] = $factura->getTipoComprobante();
            $datos['factura']['total'] = ($factura->getTotal() / 100);
            $datos['factura']['RegimenFiscal'] = $factura->getEmisor()->getRegimenFiscal();

            /**
             * Conceptos
             *
             * @var Facturacion\Concepto $concepto
             */
            foreach ($factura->getConceptos() as $i => $concepto) {
                $datos['conceptos'][$i]['cantidad'] = $concepto->getCantidad();
                $datos['conceptos'][$i]['ID'] = ($i + 1);
                $datos['conceptos'][$i]['Descuento'] = ($concepto->getDescuento() / 100);
                $datos['conceptos'][$i]['descripcion'] = $concepto->getDescripcion();
                $datos['conceptos'][$i]['valorunitario'] = ($concepto->getValorunitario() / 100);
                $datos['conceptos'][$i]['importe'] = ($concepto->getSubtotal() / 100);
                $datos['conceptos'][$i]['ClaveProdServ'] = $concepto->getClaveProdServ()->getClaveProdServ();
                $datos['conceptos'][$i]['ClaveUnidad'] = $concepto->getClaveUnidad()->getClaveUnidad();
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
        }

        /**
         * Ubicacion del timbrado
         */
        $xmlname = $factura->getFolioCotizacion() ?: md5(uniqid(rand(time(), date('dmY')), true));
        $datos['cfdi'] = $this->dir . '/web/timbrados/factura_' .  $xmlname  . '.xml';
        $datos['xml_debug'] = $this->dir . '/web/timbrados/factura_' . $factura->getFolioCotizacion() . '_sintimbrar.xml';

        return mf_genera_cfdi($datos);
    }

    public function cancela(Facturacion $factura)
    {
        require_once 'sdk2.php';

        /**
         * PAC
         */
        $datos['PAC']['usuario'] = $factura->getEmisor()->getUsuarioPAC();
        $datos['PAC']['pass'] = $factura->getEmisor()->getPasswordPAC();
        $datos['PAC']['produccion'] = $this->prod;

        $datos['cancelar'] = 'NO';
        $datos['cfdi'] = $factura->getXmlArchivo();
        $datos['conf']['cer'] = __DIR__ . '/certificados/' . $factura->getEmisor()->getCer();
        $datos['conf']['key'] = __DIR__ . '/certificados/' . $factura->getEmisor()->getKey();
        $datos['conf']['pass'] = $factura->getEmisor()->getPassword();

        return cfdi_cancelar($datos);
    }
}