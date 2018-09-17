<?php

namespace AppBundle\Entity\Contabilidad\Facturacion;

use AppBundle\Entity\Contabilidad\Facturacion;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pago
 *
 * @ORM\Table(name="contabilidad_facturacion_pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Facturacion\PagoRepository")
 */
class Pago
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /*------------------------------------------------------------------------------------------------
     * DATOS DEL PAGO COMO FACTURA
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var string
     *
     * @ORM\Column(name="serie", type="string", length=5)
     */
    private $serie;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="folio", type="integer")
     */
    private $folio;

    /**
     * @var string
     *
     * @ORM\Column(name="moneda", type="string", length=3)
     */
    private $moneda;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_comprobante", type="string", length=10)
     */
    private $tipocomprobante;

    /**
     * @var string
     *
     * @ORM\Column(name="lugar_expedicion", type="string", length=10)
     */
    private $lugarExpedicion;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal", type="bigint")
     */
    private $subtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

//    private $confirmacion;

    /*------------------------------------------------------------------------------------------------
     * DATOS DE DOCUMENTO RELACIONADO (factura)
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var string = DoctoRelacionado['idDocumento']
     *
     * @ORM\Column(name="uuid_factura_relacionada", type="string", length=60)
     */
    private $uuidFacturaRelacionada;

    /**
     * @var string
     *
     * @ORM\Column(name="moneda_factura_relacionada", type="string", length=3)
     */
    private $monedaFacturaRelacionada;

    /**
     * @var string
     *
     * @ORM\Column(name="metodo_pago_factura_relacionada", type="string", length=10)
     */
    private $metodoPagoFacturaRelacionada;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_cambio_factura_relacionada", type="integer")
     */
    private $tipoCambioFacturaRelacionada;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_parcialidad", type="integer")
     */
    private $numeroParcialidad;

    /**
     * @var integer
     *
     * @ORM\Column(name="importe_saldo_anterior", type="bigint")
     */
    private $importeSaldoAnterior;

    /**
     * @var integer
     *
     * @ORM\Column(name="importe_pagado", type="bigint")
     */
    private $importePagado;

    /**
     * @var integer
     *
     * @ORM\Column(name="importe_saldo_insoluto", type="bigint")
     */
    private $importeSaldoInsoluto;

    /*------------------------------------------------------------------------------------------------
     * DATOS DE DOCUMENTO RELACIONADO (factura->pagos)
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="fecha_pagos", type="datetime")
     */
    private $fechaPagos;

    /**
     * @var string
     *
     * @ORM\Column(name="forma_pago_pagos", type="string", length=100)
     */
    private $formaPagoPagos;

    /**
     * @var string
     *
     * @ORM\Column(name="moneda_pagos", type="string", length=3)
     */
    private $monedaPagos;

    /**
     * @var integer
     *
     * @ORM\Column(name="monto_pagos", type="bigint")
     */
    private $montoPagos;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_cambio_pagos", type="integer")
     */
    private $tipoCambioPagos;

    /*------------------------------------------------------------------------------------------------
     * DATOS DE ENTIDAD
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_cancelado", type="boolean")
     */
    private $isCancelado;

    /*------------------------------------------------------------------------------------------------
     * DATOS DE TIMBRADO CONFIRMADO
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var string $uuidFiscal = UUID de timbrado;
     *
     * @ORM\Column(name="uuid_fiscal", type="string")
     */
    private $uuidFiscal;

    /**
     * @var string $fechaTimbrado = representacion_impresa_fecha_timbrado
     *
     * @ORM\Column(name="fecha_timbrado", type="string")
     */
    private $fechaTimbrado;

    /**
     * @var string $cadenaOriginal = representacion_impresa_cadena
     *
     * @ORM\Column(name="cadena_original", type="text")
     */
    private $cadenaOriginal;

    /**
     * @var string $serieCertificadoCSD = representacion_impresa_certificado_no
     *
     * @ORM\Column(name="serie_certificado_csd", type="string")
     */
    private $serieCertificadoCSD;

    /**
     * @var string $selloCFDI = representacion_impresa_sello
     *
     * @ORM\Column(name="sello_cfdi", type="text")
     */
    private $selloCFDI;

    /**
     * @var string $selloSAT = representacion_impresa_selloSAT
     *
     * @ORM\Column(name="sello_sat", type="text")
     */
    private $selloSAT;

    /**
     * @var string $certificadoSAT = representacion_impresa_certificadoSAT
     *
     * @ORM\Column(name="certificado_sat", type="text")
     */
    private $certificadoSAT;

    /**
     * @var string $xml = cfdi
     *
     * @ORM\Column(name="xml", type="text")
     */
    private $xml;

    /**
     * @var string $png = png
     *
     * @ORM\Column(name="png", type="text")
     */
    private $png;

    /**
     * @var string $xmlArchivo = archivo_xml
     *
     * @ORM\Column(name="xml_archivo", type="string")
     */
    private $xmlArchivo;

    /**
     * @var string $pngArchivo = archivo_png
     *
     * @ORM\Column(name="png_archivo", type="string")
     */
    private $pngArchivo;

    /**
     * @var Facturacion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion")
     */
    private $factura;

//    private $conceptos; se requieren o siempre sera uno?

    public function __construct(Facturacion $factura)
    {
        $this->factura = $factura;
        $this->isCancelado = false;

        $this->serie = ''; // FIXME De donde sale la serie?
        $this->fecha = new \DateTime();
        $this->tipocomprobante = 'P';
        $this->subtotal = 0.00;
        $this->total = 0.00;
        $this->moneda = Facturacion::$monedas['MXN'];

        if ($factura->getId()) {
            $this->lugarExpedicion = $factura->getLugarExpedicion();

            $this->uuidFacturaRelacionada = $factura->getUuidFiscal();
            $this->monedaFacturaRelacionada = $factura->getMoneda();
            $this->metodoPagoFacturaRelacionada = $factura->getMetodoPago();
            $this->tipoCambioFacturaRelacionada = $factura->getTipoCambio();
        }

        $this->tipoCambioPagos = '100';
        $this->monedaPagos = Facturacion::$monedas['MXN'];
        $this->fechaPagos = new \DateTime();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set serie.
     *
     * @param string $serie
     *
     * @return Pago
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie.
     *
     * @return string
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Pago
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set folio.
     *
     * @param int $folio
     *
     * @return Pago
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;

        return $this;
    }

    /**
     * Get folio.
     *
     * @return int
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * Set moneda.
     *
     * @param string $moneda
     *
     * @return Pago
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda.
     *
     * @return string
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set tipocomprobante.
     *
     * @param string $tipocomprobante
     *
     * @return Pago
     */
    public function setTipocomprobante($tipocomprobante)
    {
        $this->tipocomprobante = $tipocomprobante;

        return $this;
    }

    /**
     * Get tipocomprobante.
     *
     * @return string
     */
    public function getTipocomprobante()
    {
        return $this->tipocomprobante;
    }

    /**
     * Set lugarExpedicion.
     *
     * @param string $lugarExpedicion
     *
     * @return Pago
     */
    public function setLugarExpedicion($lugarExpedicion)
    {
        $this->lugarExpedicion = $lugarExpedicion;

        return $this;
    }

    /**
     * Get lugarExpedicion.
     *
     * @return string
     */
    public function getLugarExpedicion()
    {
        return $this->lugarExpedicion;
    }

    /**
     * Set subtotal.
     *
     * @param int $subtotal
     *
     * @return Pago
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal.
     *
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set total.
     *
     * @param int $total
     *
     * @return Pago
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set uuidFacturaRelacionada.
     *
     * @param string $uuidFacturaRelacionada
     *
     * @return Pago
     */
    public function setUuidFacturaRelacionada($uuidFacturaRelacionada)
    {
        $this->uuidFacturaRelacionada = $uuidFacturaRelacionada;

        return $this;
    }

    /**
     * Get uuidFacturaRelacionada.
     *
     * @return string
     */
    public function getUuidFacturaRelacionada()
    {
        return $this->uuidFacturaRelacionada;
    }

    /**
     * Set monedaFacturaRelacionada.
     *
     * @param string $monedaFacturaRelacionada
     *
     * @return Pago
     */
    public function setMonedaFacturaRelacionada($monedaFacturaRelacionada)
    {
        $this->monedaFacturaRelacionada = $monedaFacturaRelacionada;

        return $this;
    }

    /**
     * Get monedaFacturaRelacionada.
     *
     * @return string
     */
    public function getMonedaFacturaRelacionada()
    {
        return $this->monedaFacturaRelacionada;
    }

    /**
     * Set metodoPagoFacturaRelacionada.
     *
     * @param string $metodoPagoFacturaRelacionada
     *
     * @return Pago
     */
    public function setMetodoPagoFacturaRelacionada($metodoPagoFacturaRelacionada)
    {
        $this->metodoPagoFacturaRelacionada = $metodoPagoFacturaRelacionada;

        return $this;
    }

    /**
     * Get metodoPagoFacturaRelacionada.
     *
     * @return string
     */
    public function getMetodoPagoFacturaRelacionada()
    {
        return $this->metodoPagoFacturaRelacionada;
    }

    /**
     * Set numeroParcialidad.
     *
     * @param int $numeroParcialidad
     *
     * @return Pago
     */
    public function setNumeroParcialidad($numeroParcialidad)
    {
        $this->numeroParcialidad = $numeroParcialidad;

        return $this;
    }

    /**
     * Get numeroParcialidad.
     *
     * @return int
     */
    public function getNumeroParcialidad()
    {
        return $this->numeroParcialidad;
    }

    /**
     * Set importeSaldoAnterior.
     *
     * @param int $importeSaldoAnterior
     *
     * @return Pago
     */
    public function setImporteSaldoAnterior($importeSaldoAnterior)
    {
        $this->importeSaldoAnterior = $importeSaldoAnterior;

        return $this;
    }

    /**
     * Get importeSaldoAnterior.
     *
     * @return int
     */
    public function getImporteSaldoAnterior()
    {
        return $this->importeSaldoAnterior;
    }

    /**
     * Set importePagado.
     *
     * @param int $importePagado
     *
     * @return Pago
     */
    public function setImportePagado($importePagado)
    {
        $this->importePagado = $importePagado;

        return $this;
    }

    /**
     * Get importePagado.
     *
     * @return int
     */
    public function getImportePagado()
    {
        return $this->importePagado;
    }

    /**
     * Set importeSaldoInsoluto.
     *
     * @param int $importeSaldoInsoluto
     *
     * @return Pago
     */
    public function setImporteSaldoInsoluto($importeSaldoInsoluto)
    {
        $this->importeSaldoInsoluto = $importeSaldoInsoluto;

        return $this;
    }

    /**
     * Get importeSaldoInsoluto.
     *
     * @return int
     */
    public function getImporteSaldoInsoluto()
    {
        return $this->importeSaldoInsoluto;
    }

    /**
     * Set fechaPagos.
     *
     * @param \DateTime $fechaPagos
     *
     * @return Pago
     */
    public function setFechaPagos($fechaPagos)
    {
        $this->fechaPagos = $fechaPagos;

        return $this;
    }

    /**
     * Get fechaPagos.
     *
     * @return \DateTime
     */
    public function getFechaPagos()
    {
        return $this->fechaPagos;
    }

    /**
     * Set formaPagoPagos.
     *
     * @param string $formaPagoPagos
     *
     * @return Pago
     */
    public function setFormaPagoPagos($formaPagoPagos)
    {
        $this->formaPagoPagos = $formaPagoPagos;

        return $this;
    }

    /**
     * Get formaPagoPagos.
     *
     * @return string
     */
    public function getFormaPagoPagos()
    {
        return $this->formaPagoPagos;
    }

    public function getFormaPagoPagosValue()
    {
        return array_flip(Facturacion::$formasPagos)[$this->formaPagoPagos];
    }

    /**
     * Set monedaPagos.
     *
     * @param string $monedaPagos
     *
     * @return Pago
     */
    public function setMonedaPagos($monedaPagos)
    {
        $this->monedaPagos = $monedaPagos;

        return $this;
    }

    /**
     * Get monedaPagos.
     *
     * @return string
     */
    public function getMonedaPagos()
    {
        return $this->monedaPagos;
    }

    /**
     * Set montoPagos.
     *
     * @param int $montoPagos
     *
     * @return Pago
     */
    public function setMontoPagos($montoPagos)
    {
        $this->montoPagos = $montoPagos;

        return $this;
    }

    /**
     * Get montoPagos.
     *
     * @return int
     */
    public function getMontoPagos()
    {
        return $this->montoPagos;
    }

    /**
     * @return int
     */
    public function getTipoCambioPagos()
    {
        return $this->tipoCambioPagos;
    }

    /**
     * @param int $tipoCambioPagos
     */
    public function setTipoCambioPagos($tipoCambioPagos)
    {
        $this->tipoCambioPagos = $tipoCambioPagos;
    }

    /**
     * @return int
     */
    public function getTipoCambioFacturaRelacionada()
    {
        return $this->tipoCambioFacturaRelacionada;
    }

    /**
     * @param int $tipoCambioFacturaRelacionada
     */
    public function setTipoCambioFacturaRelacionada($tipoCambioFacturaRelacionada)
    {
        $this->tipoCambioFacturaRelacionada = $tipoCambioFacturaRelacionada;
    }

    /**
     * @return bool
     */
    public function isCancelado()
    {
        return $this->isCancelado;
    }

    /**
     * @param bool $isCancelado
     */
    public function setIsCancelado($isCancelado)
    {
        $this->isCancelado = $isCancelado;
    }

    /**
     * Set uuidFiscal.
     *
     * @param string $uuidFiscal
     *
     * @return Pago
     */
    public function setUuidFiscal($uuidFiscal)
    {
        $this->uuidFiscal = $uuidFiscal;

        return $this;
    }

    /**
     * Get uuidFiscal.
     *
     * @return string
     */
    public function getUuidFiscal()
    {
        return $this->uuidFiscal;
    }

    /**
     * Set fechaTimbrado.
     *
     * @param string $fechaTimbrado
     *
     * @return Pago
     */
    public function setFechaTimbrado($fechaTimbrado)
    {
        $this->fechaTimbrado = $fechaTimbrado;

        return $this;
    }

    /**
     * Get fechaTimbrado.
     *
     * @return string
     */
    public function getFechaTimbrado()
    {
        return $this->fechaTimbrado;
    }

    /**
     * Set cadenaOriginal.
     *
     * @param string $cadenaOriginal
     *
     * @return Pago
     */
    public function setCadenaOriginal($cadenaOriginal)
    {
        $this->cadenaOriginal = $cadenaOriginal;

        return $this;
    }

    /**
     * Get cadenaOriginal.
     *
     * @return string
     */
    public function getCadenaOriginal()
    {
        return $this->cadenaOriginal;
    }

    /**
     * Set serieCertificadoCSD.
     *
     * @param string $serieCertificadoCSD
     *
     * @return Pago
     */
    public function setSerieCertificadoCSD($serieCertificadoCSD)
    {
        $this->serieCertificadoCSD = $serieCertificadoCSD;

        return $this;
    }

    /**
     * Get serieCertificadoCSD.
     *
     * @return string
     */
    public function getSerieCertificadoCSD()
    {
        return $this->serieCertificadoCSD;
    }

    /**
     * Set selloCFDI.
     *
     * @param string $selloCFDI
     *
     * @return Pago
     */
    public function setSelloCFDI($selloCFDI)
    {
        $this->selloCFDI = $selloCFDI;

        return $this;
    }

    /**
     * Get selloCFDI.
     *
     * @return string
     */
    public function getSelloCFDI()
    {
        return $this->selloCFDI;
    }

    /**
     * Set selloSAT.
     *
     * @param string $selloSAT
     *
     * @return Pago
     */
    public function setSelloSAT($selloSAT)
    {
        $this->selloSAT = $selloSAT;

        return $this;
    }

    /**
     * Get selloSAT.
     *
     * @return string
     */
    public function getSelloSAT()
    {
        return $this->selloSAT;
    }

    /**
     * Set certificadoSAT.
     *
     * @param string $certificadoSAT
     *
     * @return Pago
     */
    public function setCertificadoSAT($certificadoSAT)
    {
        $this->certificadoSAT = $certificadoSAT;

        return $this;
    }

    /**
     * Get certificadoSAT.
     *
     * @return string
     */
    public function getCertificadoSAT()
    {
        return $this->certificadoSAT;
    }

    /**
     * Set xml.
     *
     * @param string $xml
     *
     * @return Pago
     */
    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }

    /**
     * Get xml.
     *
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Set png.
     *
     * @param string $png
     *
     * @return Pago
     */
    public function setPng($png)
    {
        $this->png = $png;

        return $this;
    }

    /**
     * Get png.
     *
     * @return string
     */
    public function getPng()
    {
        return $this->png;
    }

    /**
     * Set xmlArchivo.
     *
     * @param string $xmlArchivo
     *
     * @return Pago
     */
    public function setXmlArchivo($xmlArchivo)
    {
        $this->xmlArchivo = $xmlArchivo;

        return $this;
    }

    /**
     * Get xmlArchivo.
     *
     * @return string
     */
    public function getXmlArchivo()
    {
        return $this->xmlArchivo;
    }

    /**
     * Set pngArchivo.
     *
     * @param string $pngArchivo
     *
     * @return Pago
     */
    public function setPngArchivo($pngArchivo)
    {
        $this->pngArchivo = $pngArchivo;

        return $this;
    }

    /**
     * Get pngArchivo.
     *
     * @return string
     */
    public function getPngArchivo()
    {
        return $this->pngArchivo;
    }

    /**
     * Set factura.
     *
     * @param Facturacion|null $factura
     *
     * @return Pago
     */
    public function setFactura(Facturacion $factura = null)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura.
     *
     * @return Facturacion|null
     */
    public function getFactura()
    {
        return $this->factura;
    }
}
