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
    private $webDirectory;
    private $version;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->directory = $kernel->getRootDir().'/../src/Hyperion/MultifacturasBundle/src';
        $this->webDirectory = $kernel->getRootDir().'/../web';
        $this->environment = $kernel->getEnvironment() === 'PROD' ? 'SI' : 'NO';
        $this->version = '3.3';
    }

    /**
     * @param Facturacion $factura
     *
     * @return Facturacion|array
     */
    public function procesa(Facturacion $factura)
    {
        /** @var Facturacion\Concepto $concepto */
        $conceptos = $factura->getConceptos();
        $emisor = $factura->getEmisor();
        $receptor = $factura->getReceptor();

        $datos = [];

        // Se especifica la version de CFDi 3.3
        $datos['version_cfdi'] = $this->version;
        $datos['cfdi'] = $this->webDirectory."/timbrados/factura-{$factura->getFolio()}.xml";
        $datos['xml_debug'] = $this->webDirectory."/timbrados/factura-{$factura->getFolio()}-sintimbre.xml";

        $datos['PAC']['produccion'] = $this->environment;
        $datos['PAC']['usuario'] = $emisor->getUsuarioPAC();
        $datos['PAC']['pass'] = $emisor->getPasswordPAC();
        $datos['conf']['cer'] = $this->directory.'/certificados/'.$emisor->getCer();
        $datos['conf']['key'] = $this->directory.'/certificados/'.$emisor->getKey();
        $datos['conf']['pass'] = $emisor->getPassword();

        // Datos de facturacion
        $datos['factura']['descuento'] = '0.00';
        $datos['factura']['moneda'] = $factura->getMoneda();
        $datos['factura']['serie'] = $factura->getSerie();
        $datos['factura']['tipocambio'] = ($factura->getTipoCambio() / 100);

        $datos['factura']['condicionesDePago'] = $factura->getCondicionesPago();
        $datos['factura']['fecha_expedicion'] = $factura->getFecha()->format('Y-m-d\TH:i:s');
        $datos['factura']['folio'] = $factura->getFolio();
        $datos['factura']['forma_pago'] = $factura->getFormaPago();
        $datos['factura']['LugarExpedicion'] = $factura->getLugarExpedicion();
        $datos['factura']['metodo_pago'] = $factura->getMetodoPago();
        $datos['factura']['tipocomprobante'] = $factura->getTipoComprobante();
        $datos['factura']['RegimenFiscal'] = $emisor->getRegimenFiscal();

        $datos['factura']['subtotal'] = ($factura->getSubtotal() / 100);
        $datos['factura']['total'] = ($factura->getTotal() / 100);

        // Datos de emisor
        $datos['emisor']['rfc'] = $emisor->getRfc();
        $datos['emisor']['nombre'] = $emisor->getNombre();

        // Datos del receptor
        $datos['receptor']['rfc'] = $receptor->getRfc();
        $datos['receptor']['nombre'] = $receptor->getRazonSocial();
        $datos['receptor']['UsoCFDI'] = $receptor->getUsoCFDI();

        // Datos de los conceptos
        foreach ($conceptos as $i => $concepto) {
            $datos['conceptos'][$i]['cantidad'] = $concepto->getCantidad();
            $datos['conceptos'][$i]['unidad'] = $concepto->getUnidad();
            $datos['conceptos'][$i]['ID'] = '1726';
            $datos['conceptos'][$i]['descripcion'] = $concepto->getDescripcion();
            $datos['conceptos'][$i]['valorunitario'] = ($concepto->getValorUnitario() / 100);
            $datos['conceptos'][$i]['importe'] = ($concepto->getImporte() / 100);
            $datos['conceptos'][$i]['ClaveProdServ'] = $concepto->getClaveProdServ()->getClaveProdServ();
            $datos['conceptos'][$i]['ClaveUnidad'] = $concepto->getClaveUnidad()->getClaveUnidad();

            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Base'] = ($concepto->getBase() / 100);
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Impuesto'] = $concepto->getImpuesto();
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['TipoFactor'] = $concepto->getTipoFactor();
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['TasaOCuota'] = $concepto->getTasaOCuota();
            $datos['conceptos'][$i]['Impuestos']['Traslados'][0]['Importe'] = ($concepto->getImpuestoImporte() / 100);
        }

        // Datos de los impuestos de la facturacion
        $datos['impuestos']['translados'][0]['impuesto'] = $factura->getImpuesto();
        $datos['impuestos']['translados'][0]['tasa'] = $factura->getTasa();
        $datos['impuestos']['translados'][0]['importe'] = ($factura->getImporte() / 100);
        $datos['impuestos']['translados'][0]['TipoFactor'] = $factura->getTipoFactor();

        $datos['impuestos']['TotalImpuestosTrasladados'] = ($factura->getTotalImpuestosTransladados() / 100);

        require_once 'sdk2.php';
        $timbrado = mf_genera_cfdi($datos);

        $factura->setXml(trim($timbrado['cfdi']));
        $factura->setPng(trim($timbrado['png']));
        $factura->setXmlArchivo($timbrado['archivo_xml']);
        $factura->setPngArchivo($timbrado['archivo_png']);
        $factura->setUuidFiscal($timbrado['uuid']);
        $factura->setCadenaOriginal($timbrado['representacion_impresa_cadena']);
        $factura->setSerieCertificadoCSD($timbrado['representacion_impresa_certificado_no']);
        $factura->setFechaTimbrado((string)$timbrado['representacion_impresa_fecha_timbrado']);
        $factura->setSelloCFDI((string)$timbrado['representacion_impresa_sello']);
        $factura->setSelloSAT((string)$timbrado['representacion_impresa_selloSAT']);
        $factura->setCertificadoSAT((string)$timbrado['representacion_impresa_certificadoSAT']);

        if (key_exists('codigo_mf_numero', $timbrado) && $timbrado['codigo_mf_numero'] === 0) {
            return $factura;
        }

        return $timbrado;
    }

    /**
     * @param Facturacion\Pago $pago
     *
     * @return Facturacion\Pago|array
     */
    public function procesaPago(Facturacion\Pago $pago)
    {
        $factura = $pago->getFactura();
        $cuentaOrdenante = $pago->getCuentaOrdenante();
        $cuentaBeneficiario = $pago->getCuentaBeneficiario();
        $emisor = $factura->getEmisor();
        $receptor = $factura->getReceptor();

        $datos['version_cfdi'] = $this->version;
        $datos['cfdi'] = $this->webDirectory."/timbrados/factura-pago-{$pago->getNumeroParcialidad()}-{$factura->getFolio()}.xml";
        $datos['xml_debug'] = $this->webDirectory."/timbrados/factura-pago-{$pago->getNumeroParcialidad()}-{$factura->getFolio()}-sintimbre.xml";

        $datos['PAC']['produccion'] = $this->environment;
        $datos['PAC']['usuario'] = $emisor->getUsuarioPAC();
        $datos['PAC']['pass'] = $emisor->getPasswordPAC();
        $datos['conf']['cer'] = $this->directory.'/certificados/'.$emisor->getCer();
        $datos['conf']['key'] = $this->directory.'/certificados/'.$emisor->getKey();
        $datos['conf']['pass'] = $emisor->getPassword();

        // Datos de la Factura
        $datos['factura']['serie'] = $pago->getSerie();
        $datos['factura']['fecha_expedicion'] = $pago->getFecha()->format('Y-m-d\TH:i:s');
        $datos['factura']['folio'] = $pago->getFolio(); // '5001';

        $datos['factura']['subtotal'] = $pago->getSubtotal();
        $datos['factura']['total'] = $pago->getTotal();
        $datos['factura']['moneda'] = $pago->getMoneda(); // 'XXX';
        $datos['factura']['tipocomprobante'] = $pago->getTipocomprobante();
        $datos['factura']['LugarExpedicion'] = $pago->getLugarExpedicion();
        $datos['factura']['RegimenFiscal'] = $emisor->getRegimenFiscal();
//        $datos['factura']['Confirmacion'] = '12345';

        // Datos de emisor
        $datos['emisor']['rfc'] = $emisor->getRfc();
        $datos['emisor']['nombre'] = $emisor->getNombre();

        // Datos del receptor
        $datos['receptor']['rfc'] = $receptor->getRfc();
        $datos['receptor']['nombre'] = $receptor->getRazonSocial();
        $datos['receptor']['UsoCFDI'] = $receptor->getUsoCFDI();

        // Se agregan los conceptos
        $datos['conceptos'][0]['ClaveProdServ'] = '84111506';
        $datos['conceptos'][0]['cantidad'] = '1';
//        $datos['conceptos'][0]['unidad'] = 'ACT';
        $datos['conceptos'][0]['ClaveUnidad'] = 'ACT';
        $datos['conceptos'][0]['descripcion'] = 'Pago';
        $datos['conceptos'][0]['valorunitario'] = '0.00';
        $datos['conceptos'][0]['importe'] = '0.00';

        // Complemento de Pagos 1.0
        $datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['IdDocumento'] = $pago->getUuidFacturaRelacionada();
        $datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['MonedaDR'] = $pago->getMonedaFacturaRelacionada();
        $datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['TipoCambioDR'] = $pago->getTipoCambioFacturaRelacionada();
        $datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['MetodoDePagoDR'] = $pago->getMetodoPagoFacturaRelacionada();
        $datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['NumParcialidad'] = $pago->getNumeroParcialidad();
        $datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['ImpSaldoAnt'] = $pago->getImporteSaldoAnterior();
        $datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['ImpPagado'] = $pago->getImportePagado();
        $datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['ImpSaldoInsoluto'] = $pago->getImporteSaldoInsoluto();

        $datos['pagos10']['Pagos'][0]['FechaPago'] = $pago->getFecha()->format('Y-m-d\TH:i:s');
        $datos['pagos10']['Pagos'][0]['FormaDePagoP'] = $pago->getFormaPagoPagos();
        $datos['pagos10']['Pagos'][0]['MonedaP'] = $pago->getMonedaPagos();
        $datos['pagos10']['Pagos'][0]['Monto'] = $pago->getMontoPagos();
        $datos['pagos10']['Pagos'][0]['TipoCambioP'] = $pago->getTipoCambioPagos();

        $datos['pagos10']['Pagos'][0]['RfcEmisorCtaOrd'] = $cuentaOrdenante->getBanco()->getRFC();
        $datos['pagos10']['Pagos'][0]['NomBancoOrdExt'] = $cuentaOrdenante->getBanco()->getRazonSocial();
        $datos['pagos10']['Pagos'][0]['CtaOrdenante'] = $cuentaOrdenante->getNumeroCuenta();

        $datos['pagos10']['Pagos'][0]['RfcEmisorCtaBen'] = $cuentaBeneficiario->getRfc();
        $datos['pagos10']['Pagos'][0]['CtaBeneficiario'] = $cuentaBeneficiario->getNumCuenta();

//        $datos['pagos10']['Pagos'][0]['NumOperacion']= '0.0';
//        $datos['pagos10']['Pagos'][0]['TipoCadPago']= '0.0';
//        $datos['pagos10']['Pagos'][0]['CertPago']= '0.0';
//        $datos['pagos10']['Pagos'][0]['CadPago']= '0.0';
//        $datos['pagos10']['Pagos'][0]['SelloPago']= '0.0';

        require_once 'sdk2.php';

        $timbrado = mf_genera_cfdi($datos);

        $pago->setXml(trim($timbrado['cfdi']));
        $pago->setPng(trim($timbrado['png']));
        $pago->setXmlArchivo($timbrado['archivo_xml']);
        $pago->setPngArchivo($timbrado['archivo_png']);
        $pago->setUuidFiscal($timbrado['uuid']);
        $pago->setCadenaOriginal($timbrado['representacion_impresa_cadena']);
        $pago->setSerieCertificadoCSD($timbrado['representacion_impresa_certificado_no']);
        $pago->setFechaTimbrado((string)$timbrado['representacion_impresa_fecha_timbrado']);
        $pago->setSelloCFDI((string)$timbrado['representacion_impresa_sello']);
        $pago->setSelloSAT((string)$timbrado['representacion_impresa_selloSAT']);
        $pago->setCertificadoSAT((string)$timbrado['representacion_impresa_certificadoSAT']);

        if (key_exists('codigo_mf_numero', $timbrado) && $timbrado['codigo_mf_numero'] === 0) {
            return $pago;
        }

        return $timbrado;
    }

    /**
     * @param Facturacion $factura
     *
     * @return mixed
     */
    public function cancela($factura)
    {
        require_once 'sdk2.php';

        $emisor = $factura->getEmisor();

        $datos['PAC']['usuario'] = $emisor->getUsuarioPAC();
        $datos['PAC']['pass'] = $emisor->getPasswordPAC();

        $datos['modulo'] = 'cancelacion2018';
        $datos['accion'] = 'cancelar';

        $datos['produccion'] = $this->environment;

        $datos['uuid'] = $factura->getUuidFiscal();
        $datos['xml'] = $factura->getXmlArchivo();

        $datos['rfc'] = $emisor->getRfc();
        $datos['password'] = $emisor->getPassword();

        $datos['b64Cer'] = $this->directory.'/certificados/'.$emisor->getCer();
        $datos['b64Key'] = $this->directory.'/certificados/'.$emisor->getKey();

        return mf_ejecuta_modulo($datos);
    }


    /**
     * @param Facturacion\Pago $pago
     *
     * @return mixed
     */
    public function cancelaPago($pago)
    {
        require_once 'sdk2.php';

        $factura = $pago->getFactura();
        $emisor = $factura->getEmisor();

        $datos['PAC']['usuario'] = $emisor->getUsuarioPAC();
        $datos['PAC']['pass'] = $emisor->getPasswordPAC();

        $datos['modulo'] = 'cancelacion2018';
        $datos['accion'] = 'cancelar';

        $datos['produccion'] = $this->environment;

        $datos['uuid'] = $factura->getUuidFiscal();
        $datos['xml'] = $factura->getXmlArchivo();

        $datos['rfc'] = $emisor->getRfc();
        $datos['password'] = $emisor->getPassword();

        $datos['b64Cer'] = $this->directory.'/certificados/'.$emisor->getCer();
        $datos['b64Key'] = $this->directory.'/certificados/'.$emisor->getKey();

        return mf_ejecuta_modulo($datos);
    }
}
