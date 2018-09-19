<?php

namespace AppBundle\Entity\Contabilidad;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Cliente\RazonSocial;
use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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

    /*------------------------------------------------------------------------------------------------
     * DATOS DE FACTURA
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var string
     *
     * @ORM\Column(name="condiciones_pago", type="string", nullable=true)
     */
    private $condicionesPago;

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
     * @ORM\Column(name="forma_pago", type="string", length=150)
     */
    private $formaPago;

    /**
     * @var string
     *
     * @ORM\Column(name="uso_cfdi", type="string", length=30)
     */
    private $usoCFDI;

    /**
     * @var string
     *
     * @ORM\Column(name="lugar_expedicion", type="string", length=10)
     */
    private $lugarExpedicion;

    /**
     * @var string
     *
     * @ORM\Column(name="metodo_pago", type="string", length=150)
     */
    private $metodoPago;

    /**
     * @var string
     *
     * @ORM\Column(name="moneda", type="string", length=3)
     */
    private $moneda;

    /**
     * @var string
     *
     * @ORM\Column(name="serie", type="string", length=5)
     */
    private $serie;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_cambio", type="integer")
     */
    private $tipoCambio;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_comprobante", type="string", length=10)
     */
    private $tipoComprobante;

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

    /*------------------------------------------------------------------------------------------------*/

    /*------------------------------------------------------------------------------------------------
     * DATOS DE TOTAL DE IMPUESTOS (translados, deberia ser una coleccion por cada tipo de traslado, pero por ahora se maneja uno)
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var string
     *
     * @ORM\Column(name="impuesto", type="string", length=10)
     */
    private $impuesto;

    /**
     * @var string
     *
     * @ORM\Column(name="tasa", type="string", length=20)
     */
    private $tasa;

    /**
     * @var int
     *
     * @ORM\Column(name="importe", type="bigint")
     */
    private $importe;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_factor", type="string", length=20)
     */
    private $tipoFactor;

    /**
     * @var int
     *
     * @ORM\Column(name="total_impuestos_transladados", type="bigint")
     */
    private $totalImpuestosTransladados;

    /*------------------------------------------------------------------------------------------------*/

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

    /*------------------------------------------------------------------------------------------------*/


    /*------------------------------------------------------------------------------------------------
     * DATOS DE ENTIDAD
     *-----------------------------------------------------------------------------------------------*/

    // private $cotizacion Se requiere un input de cotizaciones para sacar los pagos?
    // private $cuerpoCorreo;

    /**
     * @var int $estatus
     *
     * @ORM\Column(name="cancelada", type="smallint")
     */
    private $isCancelada;

    /**
     * @var int
     *
     * @ORM\Column(name="pagada", type="smallint")
     */
    private $isPagada;

    /*------------------------------------------------------------------------------------------------*/


    /*------------------------------------------------------------------------------------------------
     * ENTIDADES RELACIONADAS
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var Emisor
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $emisor;

    /**
     * @var RazonSocial
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente\RazonSocial")
     */
    private $receptor;

    /**
     * @var Cliente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente")
     */
    private $cliente;

    /**
     * @var Concepto
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto",
     *      mappedBy="factura",
     *     cascade={"persist"}
     *     )
     */
    private $conceptos;

    public static $formasPagos = [
        'Efectivo' =>                               '01',
        'Cheque nominativo' =>                      '02',
        'Transferencia electrónica de fondos' =>    '03',
        'Tarjeta de crédito' =>                     '04',
        'Monedero electrónico' =>                   '05',
        'Dinero electrónico' =>                     '06',
        'Vales de despensa' =>                      '08',
        'Dación en pago' =>                         '12',
        'Pago por subrogación' =>                   '13',
        'Pago por consignación' =>                  '14',
        'Condonación' =>                            '15',
        'Compensación' =>                           '17',
        'Novación' =>                               '23',
        'Confusión' =>                              '24',
        'Remisión de deuda' =>                      '25',
        'Prescripción o caducidad' =>               '26',
        'A satisfacción del acreedor' =>            '27',
        'Tarjeta de débito' =>                      '28',
        'Tarjeta de servicios' =>                   '29',
        'Por definir' =>                            '99',
    ];

    public static $metodosPagos = [
        'Pago en una sola exhibición' =>        'PUE',
        'Pago en parcialidades o diferido' =>   'PPD',
    ];

    public static $regimenesFiscales = [
        'General de Ley Personas Morales' =>                                            '601',
        'Personas Morales con Fines no Lucrativos' =>                                   '603',
        'Sueldos y Salarios e Ingresos Asimilados a Salarios' =>                        '605',
        'Arrendamiento' =>                                                              '606',
        'Demás ingresos' =>                                                             '608',
        'Consolidación' =>                                                              '609',
        'Residentes en el Extranjero sin Establecimiento Permanente en México' =>       '610',
        'Ingresos por Dividendos (socios y accionistas)' =>                             '611',
        'Personas Físicas con Actividades Empresariales y Profesionales' =>             '612',
        'Ingresos por intereses' =>                                                     '614',
        'Sin obligaciones fiscales' =>                                                  '616',
        'Sociedades Cooperativas de Producción que optan por diferir sus ingresos' =>   '620',
        'Incorporación Fiscal' =>                                                       '621',
        'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras' =>                   '622',
        'Opcional para Grupos de Sociedades' =>                                         '623',
        'Coordinados' =>                                                                '624',
        'Hidrocarburos' =>                                                              '628',
        'Régimen de Enajenación o Adquisición de Bienes' =>                             '607',
        'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales' =>    '629',
        'Enajenación de acciones en bolsa de valores' =>                                '630',
        'Régimen de los ingresos por obtención de premios' =>                           '615',
    ];

    public static $tiposComprobantes = [
        'Ingreso'   =>  'I',
        'Egreso' =>     'E',
        'Traslado' =>   'T',
        'Nómina' =>     'N',
        'Pago' =>       'P',
    ];

    public static $CFDIS = [
        'Adquisición de mercancias' =>                                                              'G01',
        'Devoluciones, descuentos o bonificaciones' =>                                              'G02',
        'Gastos en general' =>                                                                      'G03',
        'Construcciones' =>                                                                         'I01',
        'Mobilario y equipo de oficina por inversiones' =>                                          'I02',
        'Equipo de transporte' =>                                                                   'I03',
        'Equipo de computo y accesorios' =>                                                         'I04',
        'Dados, troqueles, moldes, matrices y herramental' =>                                       'I05',
        'Comunicaciones telefónicas' =>                                                             'I06',
        'Comunicaciones satelitales' =>                                                             'I07',
        'Otra maquinaria y equipo' =>                                                               'I08',
        'Honorarios médicos, dentales y gastos hospitalarios.' =>                                   'D01',
        'Gastos médicos por incapacidad o discapacidad' =>                                          'D02',
        'Gastos funerales.' =>                                                                      'D03',
        'Donativos.' =>                                                                             'D04',
        'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).' =>    'D05',
        'Aportaciones voluntarias al SAR.' =>                                                       'D06',
        'Primas por seguros de gastos médicos.' =>                                                  'D07',
        'Gastos de transportación escolar obligatoria.' =>                                          'D08',
        'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.' =>  'D09',
        'Pagos por servicios educativos (colegiaturas)' =>                                          'D10',
        'Por definir' =>                                                                            'P01',
    ];

    public static $impuestos = [
        'ISR' =>    '001',
        'IVA' =>    '002',
        'IEPS' =>   '003',
    ];

    public static $factores = [
        'Tasa' => 'Tasa',
        'Cuota' => 'Cuota',
    ];

    public static $monedas = [
        'USD' => 'USD',
        'MXN' => 'MXN'
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->folio = 000000;

        $this->formaPago = self::$formasPagos['Efectivo'];
        $this->metodoPago = self::$metodosPagos['Pago en una sola exhibición'];
        $this->usoCFDI = self::$CFDIS['Gastos en general'];
        $this->impuesto = self::$impuestos['IVA'];
        $this->tipoFactor = self::$factores['Tasa'];
        $this->importe = 0;
        $this->totalImpuestosTransladados = 0;

        $this->tasa = '0.160000';
        $this->lugarExpedicion = '77500';
        $this->tipoCambio = 100;
        $this->serie = '';
        $this->moneda = self::$monedas['MXN'];

        $this->subtotal = 0;
        $this->total = 0;

        $this->fecha = new \DateTime();
        $this->isCancelada = false;
        $this->isPagada = 0;
        $this->conceptos = new ArrayCollection();
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
     * Set condicionesPago.
     *
     * @param string|null $condicionesPago
     */
    public function setCondicionesPago($condicionesPago = null)
    {
        $this->condicionesPago = $condicionesPago;
    }

    /**
     * Get condicionesPago.
     *
     * @return string|null
     */
    public function getCondicionesPago()
    {
        return $this->condicionesPago;
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
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;
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
     * Set formaPago.
     *
     * @param string $formaPago
     */
    public function setFormaPago($formaPago)
    {
        $this->formaPago = $formaPago;
    }

    /**
     * Get formaPago.
     *
     * @return string
     */
    public function getFormaPago()
    {
        return $this->formaPago;
    }

    public function getFormaPagoValue()
    {
        return array_flip(self::$formasPagos)[$this->formaPago];
    }

    /**
     * @return string
     */
    public function getUsoCFDI()
    {
        return $this->usoCFDI;
    }

    public function getUsoCFDIValue()
    {
        return array_flip(self::$CFDIS)[$this->usoCFDI];
    }

    /**
     * @param string $usoCFDI
     */
    public function setUsoCFDI($usoCFDI)
    {
        $this->usoCFDI = $usoCFDI;
    }

    /**
     * Set lugarExpedicion.
     *
     * @param string $lugarExpedicion
     */
    public function setLugarExpedicion($lugarExpedicion)
    {
        $this->lugarExpedicion = $lugarExpedicion;
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
     * Set metodoPago.
     *
     * @param string $metodoPago
     */
    public function setMetodoPago($metodoPago)
    {
        $this->metodoPago = $metodoPago;
    }

    /**
     * Get metodoPago.
     *
     * @return string
     */
    public function getMetodoPago()
    {
        return $this->metodoPago;
    }

    public function getMetodoPagoValue()
    {
        return array_flip(self::$metodosPagos)[$this->metodoPago];
    }

    /**
     * Set moneda.
     *
     * @param string $moneda
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
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

    public function getMonedaValue()
    {
        return array_flip(self::$monedas)[$this->moneda];
    }

    /**
     * Set serie.
     *
     * @param string $serie
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
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
     * Set tipoCambio.
     *
     * @param int $tipoCambio
     */
    public function setTipoCambio($tipoCambio)
    {
        $this->tipoCambio = $tipoCambio;
    }

    /**
     * Get tipoCambio.
     *
     * @return int
     */
    public function getTipoCambio()
    {
        return $this->tipoCambio;
    }

    /**
     * Set tipoComprobante.
     *
     * @param string $tipoComprobante
     */
    public function setTipoComprobante($tipoComprobante)
    {
        $this->tipoComprobante = $tipoComprobante;
    }

    /**
     * Get tipoComprobante.
     *
     * @return string
     */
    public function getTipoComprobante()
    {
        return $this->tipoComprobante;
    }

    public function getTipoComprobanteValue()
    {
        return array_flip(self::$tiposComprobantes)[$this->tipoComprobante];
    }

    /**
     * Set subtotal.
     *
     * @param int $subtotal
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
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
     */
    public function setTotal($total)
    {
        $this->total = $total;
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
     * Set impuesto.
     *
     * @param string $impuesto
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;
    }

    /**
     * Get impuesto.
     *
     * @return string
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set tasa.
     *
     * @param string $tasa
     */
    public function setTasa($tasa)
    {
        $this->tasa = $tasa;
    }

    /**
     * Get tasa.
     *
     * @return string
     */
    public function getTasa()
    {
        return $this->tasa;
    }

    /**
     * Set importe.
     *
     * @param int $importe
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    }

    /**
     * Get importe.
     *
     * @return int
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set tipoFactor.
     *
     * @param string $tipoFactor
     */
    public function setTipoFactor($tipoFactor)
    {
        $this->tipoFactor = $tipoFactor;
    }

    /**
     * Get tipoFactor.
     *
     * @return string
     */
    public function getTipoFactor()
    {
        return $this->tipoFactor;
    }

    /**
     * @return int
     */
    public function getTotalImpuestosTransladados()
    {
        return $this->totalImpuestosTransladados;
    }

    /**
     * @param int $totalImpuestosTransladados
     */
    public function setTotalImpuestosTransladados($totalImpuestosTransladados)
    {
        $this->totalImpuestosTransladados = $totalImpuestosTransladados;
    }

    /**
     * Set folioFiscal.
     *
     * @param string $uuidFiscal
     */
    public function setUuidFiscal($uuidFiscal)
    {
        $this->uuidFiscal = $uuidFiscal;
    }

    /**
     * Get folioFiscal.
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
     */
    public function setFechaTimbrado($fechaTimbrado)
    {
        $this->fechaTimbrado = $fechaTimbrado;
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
     */
    public function setCadenaOriginal($cadenaOriginal)
    {
        $this->cadenaOriginal = $cadenaOriginal;
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
     */
    public function setSerieCertificadoCSD($serieCertificadoCSD)
    {
        $this->serieCertificadoCSD = $serieCertificadoCSD;
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
     */
    public function setSelloCFDI($selloCFDI)
    {
        $this->selloCFDI = $selloCFDI;
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
     */
    public function setSelloSAT($selloSAT)
    {
        $this->selloSAT = $selloSAT;
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
     */
    public function setCertificadoSAT($certificadoSAT)
    {
        $this->certificadoSAT = $certificadoSAT;
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
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
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
     */
    public function setPng($png)
    {
        $this->png = $png;
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
     */
    public function setXmlArchivo($xmlArchivo)
    {
        $this->xmlArchivo = $xmlArchivo;
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
     */
    public function setPngArchivo($pngArchivo)
    {
        $this->pngArchivo = $pngArchivo;
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
     * Set isCancelada.
     *
     * @param int $isCancelada
     */
    public function setIsCancelada($isCancelada)
    {
        $this->isCancelada = $isCancelada;
    }

    /**
     * Get isCancelada.
     *
     * @return int
     */
    public function isCancelada()
    {
        return $this->isCancelada;
    }

    /**
     * @param int $isPagada
     */
    public function setIsPagada($isPagada)
    {
        $this->isPagada = $isPagada;
    }

    /**
     * @return int
     */
    public function isPagada()
    {
        return $this->isPagada;
    }

    /**
     * Set emisor.
     *
     * @param Emisor|null $emisor
     */
    public function setEmisor(Emisor $emisor = null)
    {
        $this->emisor = $emisor;
    }

    /**
     * Get emisor.
     *
     * @return Emisor|null
     */
    public function getEmisor()
    {
        return $this->emisor;
    }

    /**
     * Set receptor.
     *
     * @param RazonSocial|null $receptor
     */
    public function setReceptor(RazonSocial $receptor = null)
    {
        $this->receptor = $receptor;
    }

    /**
     * Get receptor.
     *
     * @return RazonSocial|null
     */
    public function getReceptor()
    {
        return $this->receptor;
    }

    /**
     * Set cliente.
     *
     * @param Cliente|null $cliente
     */
    public function setCliente(Cliente $cliente = null)
    {
        $this->cliente = $cliente;
    }

    /**
     * Get cliente.
     *
     * @return Cliente|null
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Add concepto.
     *
     * @param Concepto $concepto
     */
    public function addConcepto(Concepto $concepto)
    {
        $concepto->setFactura($this);
        $this->conceptos[] = $concepto;
    }

    /**
     * Remove concepto.
     *
     * @param Concepto $concepto
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeConcepto(Concepto $concepto)
    {
        return $this->conceptos->removeElement($concepto);
    }

    /**
     * Get conceptos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConceptos()
    {
        return $this->conceptos;
    }
}
