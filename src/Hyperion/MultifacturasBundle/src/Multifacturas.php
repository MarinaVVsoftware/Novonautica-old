<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/9/18
 * Time: 12:35
 */

namespace Hyperion\MultifacturasBundle\src;

use AppBundle\Entity\Contabilidad\Facturacion;
use Symfony\Component\HttpKernel\KernelInterface;

class Multifacturas
{
    /**
     * @var KernelInterface
     */
    private $kernel;
    private $directory;
    private $environment;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->directory = $kernel->getRootDir().'/../src/Hyperion/MultifacturasBundle/src';
        $this->environment = $kernel->getEnvironment() === 'PROD' ? 'SI' : 'NO';
    }

    public function procesa(Facturacion $factura)
    {
        /** @var Facturacion\Concepto $concepto */
        $conceptos = $factura->getConceptos();
        $emisor = $factura->getEmisor();
        $receptor = $factura->getReceptor();

        $datos = [];

        // Se especifica la version de CFDi 3.3
        $datos['version_cfdi'] = '3.3';
        $datos['cfdi'] = $this->directory.'/timbrados/cfdi_ejemplo_factura.xml';
        $datos['xml_debug'] = $this->directory.'/timbrados/sin_timbrar_ejemplo_factura.xml';

        $datos['PAC']['produccion'] = $this->environment;
        $datos['PAC']['usuario'] = $emisor->getUsuarioPAC();
        $datos['PAC']['pass'] = $emisor->getPasswordPAC();
        $datos['conf']['cer'] = $this->directory.'/certificados/'.$emisor->getCer();
        $datos['conf']['key'] = $this->directory.'/certificados/'.$emisor->getKey();
        $datos['conf']['pass'] = $emisor->getPassword();

        // Datos de facturacion
        $datos['factura']['descuento'] = '0.00';
        $datos['factura']['moneda'] = 'MXN';
        $datos['factura']['serie'] = 'A';
        $datos['factura']['tipocambio'] = 1.00;

        $datos['factura']['condicionesDePago'] = $factura->getCondicionesPago();
//        $datos['factura']['fecha_expedicion'] = date('Y-m-d\TH:i:s', time() - 120);
        $datos['factura']['fecha_expedicion'] = $factura->getFecha()->format('Y-m-d\TH:i:s');
        $datos['factura']['folio'] = $factura->getFolio();
        $datos['factura']['forma_pago'] = $factura->getFormaPago();
        $datos['factura']['LugarExpedicion'] = $factura->getLugarExpedicion();
        $datos['factura']['metodo_pago'] = $factura->getMetodoPago();
        $datos['factura']['tipocomprobante'] = $factura->getTipoComprobante();
        $datos['factura']['RegimenFiscal'] = $emisor->getRegimenFiscal();

        $datos['factura']['subtotal'] = number_format(($factura->getSubtotal() / 100), 2);
        $datos['factura']['total'] = number_format(($factura->getTotal() / 100), 2);

        // Datos de emisor
        $datos['emisor']['rfc'] = $emisor->getRfc();
        $datos['emisor']['nombre'] = $emisor->getNombre();

        // Datos del receptor
        $datos['receptor']['rfc'] = $receptor->getRfc();
        $datos['receptor']['nombre'] = $receptor->getRazonSocial();
        $datos['receptor']['UsoCFDI'] = 'G02'; //$receptor->getUsoCFDI();

        // Datos de los conceptos

        foreach ($conceptos as $i => $concepto) {
            $datos['conceptos'][$i]['cantidad'] = $concepto->getCantidad();
            $datos['conceptos'][$i]['unidad'] = $concepto->getUnidad();
            $datos['conceptos'][$i]['ID'] = '1726';
            $datos['conceptos'][$i]['descripcion'] = $concepto->getDescripcion();
            $datos['conceptos'][$i]['valorunitario'] = number_format(($concepto->getValorUnitario() / 100), 2);
            $datos['conceptos'][$i]['importe'] = number_format(($concepto->getImporte() / 100), 2);
            $datos['conceptos'][$i]['ClaveProdServ'] = $concepto->getClaveProdServ()->getClaveProdServ();
            $datos['conceptos'][$i]['ClaveUnidad'] = $concepto->getClaveUnidad()->getClaveUnidad();

            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Base'] = number_format(($concepto->getBase() / 100), 2);
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Impuesto'] = $concepto->getImpuesto();
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['TipoFactor'] = $concepto->getTipoFactor();
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['TasaOCuota'] = $concepto->getTasaOCuota();
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Importe'] = number_format(($concepto->getImpuestoImporte() / 100), 2);
        }

        // Datos de los impuestos de la facturacion
        $datos['impuestos']['translados'][0]['impuesto'] = $factura->getImpuesto();
        $datos['impuestos']['translados'][0]['tasa'] = $factura->getTasa();
        $datos['impuestos']['translados'][0]['importe'] = number_format(($factura->getImporte() / 100), 2);
        $datos['impuestos']['translados'][0]['TipoFactor'] = $factura->getTipoFactor();

        $datos['impuestos']['TotalImpuestosTrasladados'] = number_format(
            ($factura->getTotalImpuestosTransladados() / 100),2
        );

        require_once 'sdk2.php';
        return mf_genera_cfdi($datos);
        
        // FIXME Antes de retornar los datos, rellenar lo que falta de la factura y retornar los errores si falla para trigerear el validador
    }

    public function cancela(Facturacion $factura)
    {
        require_once 'sdk2.php';

        /**
         * PAC
         */
        $datos['PAC']['usuario'] = $factura->getEmisor()->getUsuarioPAC();
        $datos['PAC']['pass'] = $factura->getEmisor()->getPasswordPAC();
        $datos['PAC']['produccion'] = 'NO';

        $datos['cancelar'] = 'NO';
        $datos['cfdi'] = $factura->getXmlArchivo();
        $datos['conf']['cer'] = __DIR__.'/certificados/'.$factura->getEmisor()->getCer();
        $datos['conf']['key'] = __DIR__.'/certificados/'.$factura->getEmisor()->getKey();
        $datos['conf']['pass'] = $factura->getEmisor()->getPassword();

        return cfdi_cancelar($datos);
    }
}
