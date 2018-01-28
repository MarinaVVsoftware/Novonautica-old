<?php

namespace AppBundle\Entity\Contabilidad;

use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto;
use AppBundle\Entity\Pago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Facturacion
 *
 * @ORM\Table(name="contabilidad_facturacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\FacturacionRepository")
 */
class Facturacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este valor no puede estar vacio")
     *
     * @ORM\Column(name="rfc", type="string", length=20)
     */
    private $rfc;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este valor no puede estar vacio")
     *
     * @ORM\Column(name="cliente", type="string", length=255)
     */
    private $cliente;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este valor no puede estar vacio")
     *
     * @ORM\Column(name="razon_social", type="string", length=255)
     */
    private $razonSocial;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este valor no puede estar vacio")
     *
     * @ORM\Column(name="direccion_fiscal", type="text")
     */
    private $direccionFiscal;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este valor no puede estar vacio")
     *
     * @ORM\Column(name="numero_telefonico", type="string", length=20)
     */
    private $numeroTelefonico;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este valor no puede estar vacio")
     * @Assert\Email(message="El valor no parece ser un correo")
     *
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="moneda", type="string", length=5)
     *
     */
    private $moneda;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_cambio", type="integer")
     */
    private $tipoCambio;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="fecha", type="datetimetz_immutable")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="descuento", type="bigint")
     */
    private $descuento;

    /**
     * @var string
     *
     * @ORM\Column(name="forma_pago", type="string", length=150)
     */
    private $formaPago;

    /**
     * @var string
     *
     * @ORM\Column(name="metodo_pago", type="string", length=150)
     */
    private $metodoPago;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal", type="bigint")
     */
    private $subtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="iva", type="bigint")
     */
    private $iva;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_comprobante", type="string", length=10)
     */
    private $tipoComprobante;

    /**
     * @var string
     *
     * @ORM\Column(name="uso_cfdi", type="string", length=10)
     */
    private $usoCFDI;

    /**
     * @var string
     *
     * @ORM\Column(name="condiciones_pago", type="string", nullable=true)
     */
    private $condicionesPago;

    /**
     * @var string $folioFiscal = uuid;
     *
     * @ORM\Column(name="folio_fiscal", type="string")
     */
    private $folioFiscal;

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
     * @var string $fechaTimbrado = representacion_impresa_fecha_timbrado
     *
     * @ORM\Column(name="fecha_timbrado", type="string")
     */
    private $fechaTimbrado;

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
     * @var string|null $folioCotizacion input de busqueda de cotizaciones
     *
     * @ORM\Column(name="folio_cotizacion", type="string", length=150, nullable=true)
     */
    private $folioCotizacion;

    /**
     * @var int $estatus
     * 1 = Creada [default],
     * 0 = Cancelada
     *
     * @ORM\Column(name="estatus", type="smallint")
     */
    private $estatus;

    /**
     * @var Emisor
     *
     * @Assert\NotNull(message="Por favor elige una opción")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $emisor;

    /**
     * @var Concepto
     *
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto", mappedBy="factura", cascade={"persist"})
     */
    private $conceptos;

    /**
     * @var Pago
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Pago", mappedBy="factura")
     */
    private $pagos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->estatus = 1;
        $this->fecha = new \DateTimeImmutable();
        $this->conceptos = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rfc
     *
     * @param string $rfc
     *
     * @return Facturacion
     */
    public function setRfc($rfc)
    {
        $this->rfc = $rfc;

        return $this;
    }

    /**
     * Get rfc
     *
     * @return string
     */
    public function getRfc()
    {
        return $this->rfc;
    }

    /**
     * Set cliente
     *
     * @param string $cliente
     *
     * @return Facturacion
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return string
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     *
     * @return Facturacion
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set direccionFiscal
     *
     * @param string $direccionFiscal
     *
     * @return Facturacion
     */
    public function setDireccionFiscal($direccionFiscal)
    {
        $this->direccionFiscal = $direccionFiscal;

        return $this;
    }

    /**
     * Get direccionFiscal
     *
     * @return string
     */
    public function getDireccionFiscal()
    {
        return $this->direccionFiscal;
    }

    /**
     * Set numeroTelefonico
     *
     * @param string $numeroTelefonico
     *
     * @return Facturacion
     */
    public function setNumeroTelefonico($numeroTelefonico)
    {
        $this->numeroTelefonico = $numeroTelefonico;

        return $this;
    }

    /**
     * Get numeroTelefonico
     *
     * @return string
     */
    public function getNumeroTelefonico()
    {
        return $this->numeroTelefonico;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Facturacion
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * @param string $moneda
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    }

    /**
     * @return int
     */
    public function getTipoCambio()
    {
        return $this->tipoCambio;
    }

    /**
     * @param int $tipoCambio
     */
    public function setTipoCambio($tipoCambio)
    {
        $this->tipoCambio = $tipoCambio;
    }

    /**
     * Set subtotal
     *
     * @param integer $subtotal
     *
     * @return Facturacion
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return integer
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set iva
     *
     * @param integer $iva
     *
     * @return Facturacion
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return integer
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set total
     *
     * @param integer $total
     *
     * @return Facturacion
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getTipoComprobante()
    {
        return $this->tipoComprobante;
    }

    /**
     * @param string $tipoComprobante
     */
    public function setTipoComprobante($tipoComprobante)
    {
        $this->tipoComprobante = $tipoComprobante;
    }

    /**
     * Set fecha
     *
     * @param \DateTimeImmutable $fecha
     *
     * @return Facturacion
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTimeImmutable
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set descuento
     *
     * @param integer $descuento
     *
     * @return Facturacion
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return integer
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set formaPago
     *
     * @param string $formaPago
     *
     * @return Facturacion
     */
    public function setFormaPago($formaPago)
    {
        $this->formaPago = $formaPago;

        return $this;
    }

    /**
     * Get formaPago
     *
     * @return string
     */
    public function getFormaPago()
    {
        return $this->formaPago;
    }

    /**
     * Set metodoPago
     *
     * @param string $metodoPago
     *
     * @return Facturacion
     */
    public function setMetodoPago($metodoPago)
    {
        $this->metodoPago = $metodoPago;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsoCFDI()
    {
        return $this->usoCFDI;
    }

    /**
     * @param string $usoCFDI
     */
    public function setUsoCFDI($usoCFDI)
    {
        $this->usoCFDI = $usoCFDI;
    }

    /**
     * Get metodoPago
     *
     * @return string
     */
    public function getMetodoPago()
    {
        return $this->metodoPago;
    }

    /**
     * @return string
     */
    public function getCondicionesPago()
    {
        return $this->condicionesPago;
    }

    /**
     * @param string $condicionesPago
     */
    public function setCondicionesPago($condicionesPago)
    {
        $this->condicionesPago = $condicionesPago;
    }

    /**
     * Set emisor
     *
     * @param Emisor $emisor
     *
     * @return Facturacion
     */
    public function setEmisor(Emisor $emisor = null)
    {
        $this->emisor = $emisor;

        return $this;
    }

    /**
     * Get emisor
     *
     * @return Emisor
     */
    public function getEmisor()
    {
        return $this->emisor;
    }

    /**
     * Set folioFiscal
     *
     * @param string $folioFiscal
     *
     * @return Facturacion
     */
    public function setFolioFiscal($folioFiscal)
    {
        $this->folioFiscal = $folioFiscal;

        return $this;
    }

    /**
     * Get folioFiscal
     *
     * @return string
     */
    public function getFolioFiscal()
    {
        return $this->folioFiscal;
    }

    /**
     * Set cadenaOriginal
     *
     * @param string $cadenaOriginal
     *
     * @return Facturacion
     */
    public function setCadenaOriginal($cadenaOriginal)
    {
        $this->cadenaOriginal = $cadenaOriginal;

        return $this;
    }

    /**
     * Get cadenaOriginal
     *
     * @return string
     */
    public function getCadenaOriginal()
    {
        return $this->cadenaOriginal;
    }

    /**
     * Set serieCertificadoCSD
     *
     * @param string $serieCertificadoCSD
     *
     * @return Facturacion
     */
    public function setSerieCertificadoCSD($serieCertificadoCSD)
    {
        $this->serieCertificadoCSD = $serieCertificadoCSD;

        return $this;
    }

    /**
     * Get serieCertificadoCSD
     *
     * @return string
     */
    public function getSerieCertificadoCSD()
    {
        return $this->serieCertificadoCSD;
    }

    /**
     * Set fechaTimbrado
     *
     * @param string $fechaTimbrado
     *
     * @return Facturacion
     */
    public function setFechaTimbrado($fechaTimbrado)
    {
        $this->fechaTimbrado = $fechaTimbrado;

        return $this;
    }

    /**
     * Get fechaTimbrado
     *
     * @return string
     */
    public function getFechaTimbrado()
    {
        return $this->fechaTimbrado;
    }

    /**
     * Set selloCFDI
     *
     * @param string $selloCFDI
     *
     * @return Facturacion
     */
    public function setSelloCFDI($selloCFDI)
    {
        $this->selloCFDI = $selloCFDI;

        return $this;
    }

    /**
     * Get selloCFDI
     *
     * @return string
     */
    public function getSelloCFDI()
    {
        return $this->selloCFDI;
    }

    /**
     * Set selloSAT
     *
     * @param string $selloSAT
     *
     * @return Facturacion
     */
    public function setSelloSAT($selloSAT)
    {
        $this->selloSAT = $selloSAT;

        return $this;
    }

    /**
     * Get selloSAT
     *
     * @return string
     */
    public function getSelloSAT()
    {
        return $this->selloSAT;
    }

    /**
     * Set certificadoSAT
     *
     * @param string $certificadoSAT
     *
     * @return Facturacion
     */
    public function setCertificadoSAT($certificadoSAT)
    {
        $this->certificadoSAT = $certificadoSAT;

        return $this;
    }

    /**
     * Get certificadoSAT
     *
     * @return string
     */
    public function getCertificadoSAT()
    {
        return $this->certificadoSAT;
    }

    /**
     * Set xml
     *
     * @param string $xml
     *
     * @return Facturacion
     */
    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }

    /**
     * Get xml
     *
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Set png
     *
     * @param string $png
     *
     * @return Facturacion
     */
    public function setPng($png)
    {
        $this->png = $png;

        return $this;
    }

    /**
     * Get png
     *
     * @return string
     */
    public function getPng()
    {
        return $this->png;
    }

    /**
     * Set xmlArchivo
     *
     * @param string $xmlArchivo
     *
     * @return Facturacion
     */
    public function setXmlArchivo($xmlArchivo)
    {
        $this->xmlArchivo = $xmlArchivo;

        return $this;
    }

    /**
     * Get xmlArchivo
     *
     * @return string
     */
    public function getXmlArchivo()
    {
        return $this->xmlArchivo;
    }

    /**
     * Set pngArchivo
     *
     * @param string $pngArchivo
     *
     * @return Facturacion
     */
    public function setPngArchivo($pngArchivo)
    {
        $this->pngArchivo = $pngArchivo;

        return $this;
    }

    /**
     * Get pngArchivo
     *
     * @return string
     */
    public function getPngArchivo()
    {
        return $this->pngArchivo;
    }

    /**
     * @return null|string
     */
    public function getFolioCotizacion()
    {
        return $this->folioCotizacion;
    }

    /**
     * @param null|string $folioCotizacion
     */
    public function setFolioCotizacion($folioCotizacion)
    {
        $this->folioCotizacion = $folioCotizacion;
    }

    /**
     * Add concepto
     *
     * @param Concepto $concepto
     *
     * @return Facturacion
     */
    public function addConcepto(Concepto $concepto)
    {
        $this->conceptos->add($concepto);
        $concepto->setFactura($this);

        return $this;
    }

    /**
     * Remove concepto
     *
     * @param Concepto $concepto
     */
    public function removeConcepto(Concepto $concepto)
    {
        $this->conceptos->removeElement($concepto);
    }

    /**
     * Get conceptos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConceptos()
    {
        return $this->conceptos;
    }

    /**
     * Set pagos
     *
     * @param Pago $pagos
     *
     * @return Facturacion
     */
    public function setPagos(Pago $pagos = null)
    {
        $this->pagos = $pagos;

        return $this;
    }

    /**
     * Get pagos
     *
     * @return Pago
     */
    public function getPagos()
    {
        return $this->pagos;
    }

    /**
     * @Assert\Callback()
     *
     * @param ExecutionContextInterface $context
     * @param $payload
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ((null !== $this->getPagos()) && ($this->getTotal() > $this->getPagos()->getCantidad())) {
            $context->buildViolation('El total es mayor que la cantidad del pago.')
                ->atPath('total')
                ->addViolation();
        }
    }

    /**
     * @return int
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * @param int $estatus
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    }
}
