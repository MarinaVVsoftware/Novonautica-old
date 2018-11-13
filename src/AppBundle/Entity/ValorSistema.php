<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ValorSistema
 *
 * @ORM\Table(name="valor_sistema")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ValorSistemaRepository")
 */
class ValorSistema
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
     * @var int
     *
     * @ORM\Column(name="dolar", type="integer")
     */
    private $dolar;

    /**
     * @var float
     * @Assert\NotBlank(
     *     message="El iva no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="iva", type="float")
     */
    private $iva;

    /**
     * @var int
     *
     * @ORM\Column(name="folio_marina", type="bigint")
     */
    private $folioMarina;

    /**
     * @var int
     *
     * @ORM\Column(name="folio_astillero", type="bigint")
     */
    private $folioAstillero;

    /**
     * @var int
     *
     * @ORM\Column(name="folio_combustible", type="bigint")
     */
    private $folioCombustible;

    /**
     * @var int
     *
     * @ORM\Column(name="folio_solicitud", type="bigint")
     */
    private $folioSolicitud;

    /**
     * @var int
     *
     * @ORM\Column(name="folio_compra", type="bigint")
     */
    private $folioCompra;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje_correo_marina", type="text")
     */
    private $mensajeCorreoMarina;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje_correo_marina_gasolina", type="text")
     */
    private $mensajeCorreoMarinaGasolina;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje_correo_astillero", type="text")
     */
    private $mensajeCorreoAstillero;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_habiles_marina_cotizacion", type="integer")
     */
    private $diasHabilesMarinaCotizacion;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_habiles_astillero_cotizacion", type="integer")
     */
    private $diasHabilesAstilleroCotizacion;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_habiles_combustible", type="integer")
     */
    private $diasHabilesCombustible;

    /**
     * @var float
     * @Assert\NotBlank(
     *     message="El porcentaje moratorio no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="porcentajeMoratorio", type="float")
     */
    private $porcentajeMoratorio;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_postal", type="string", length=6)
     */
    private $codigoPostal;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=50)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=100)
     */
    private $correo;

    /**
     * @var string
     *
     * @ORM\Column(name="terminos_marina", type="text")
     */
    private $terminosMarina;

    /**
     * @var string
     *
     * @ORM\Column(name="terminos_astillero", type="text")
     */
    private $terminosAstillero;

    /**
     * Generate a token
     *
     * @return string (100 characters)
     */
    public function generaToken($length)
    {
        //$length = 100;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $token;

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
     * @return string
     */
    public function getMensajeCorreoMarina()
    {
        return $this->mensajeCorreoMarina;
    }

    /**
     * @param string $mensajeCorreoMarina
     */
    public function setMensajeCorreoMarina($mensajeCorreoMarina)
    {
        $this->mensajeCorreoMarina = $mensajeCorreoMarina;
    }

    /**
     * @return int
     */
    public function getDolar()
    {
        return $this->dolar;
    }

    /**
     * @param int $dolar
     */
    public function setDolar($dolar)
    {
        $this->dolar = $dolar;
    }

    /**
     * @return int
     */
    public function getFolioMarina()
    {
        return $this->folioMarina;
    }

    /**
     * @param int $folioMarina
     */
    public function setFolioMarina($folioMarina)
    {
        $this->folioMarina = $folioMarina;
    }

    /**
     * @return int
     */
    public function getDiasHabilesMarinaCotizacion()
    {
        return $this->diasHabilesMarinaCotizacion;
    }

    /**
     * @param int $diasHabilesMarinaCotizacion
     */
    public function setDiasHabilesMarinaCotizacion($diasHabilesMarinaCotizacion)
    {
        $this->diasHabilesMarinaCotizacion = $diasHabilesMarinaCotizacion;
    }

    /**
     * @return float
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * @param float $iva
     */
    public function setIva($iva)
    {
        $this->iva = $iva;
    }

    /**
     * @return string
     */
    public function getMensajeCorreoMarinaGasolina()
    {
        return $this->mensajeCorreoMarinaGasolina;
    }

    /**
     * @param string $mensajeCorreoMarinaGasolina
     */
    public function setMensajeCorreoMarinaGasolina($mensajeCorreoMarinaGasolina)
    {
        $this->mensajeCorreoMarinaGasolina = $mensajeCorreoMarinaGasolina;
    }

    /**
     * @return string
     */
    public function getMensajeCorreoAstillero()
    {
        return $this->mensajeCorreoAstillero;
    }

    /**
     * @param string $mensajeCorreoAstillero
     */
    public function setMensajeCorreoAstillero($mensajeCorreoAstillero)
    {
        $this->mensajeCorreoAstillero = $mensajeCorreoAstillero;
    }

    /**
     * @return int
     */
    public function getFolioAstillero()
    {
        return $this->folioAstillero;
    }

    /**
     * @param int $folioAstillero
     */
    public function setFolioAstillero($folioAstillero)
    {
        $this->folioAstillero = $folioAstillero;
    }

    /**
     * @return int
     */
    public function getDiasHabilesAstilleroCotizacion()
    {
        return $this->diasHabilesAstilleroCotizacion;
    }

    /**
     * @param int $diasHabilesAstilleroCotizacion
     */
    public function setDiasHabilesAstilleroCotizacion($diasHabilesAstilleroCotizacion)
    {
        $this->diasHabilesAstilleroCotizacion = $diasHabilesAstilleroCotizacion;
    }



    /**
     * Set porcentajeMoratorio.
     *
     * @param float $porcentajeMoratorio
     *
     * @return ValorSistema
     */
    public function setPorcentajeMoratorio($porcentajeMoratorio)
    {
        $this->porcentajeMoratorio = $porcentajeMoratorio;

        return $this;
    }

    /**
     * Get porcentajeMoratorio.
     *
     * @return float
     */
    public function getPorcentajeMoratorio()
    {
        return $this->porcentajeMoratorio;
    }

    /**
     * Set direccion.
     *
     * @param string $direccion
     *
     * @return ValorSistema
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion.
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set codigoPostal.
     *
     * @param string $codigoPostal
     *
     * @return ValorSistema
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get codigoPostal.
     *
     * @return string
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * Set telefono.
     *
     * @param string $telefono
     *
     * @return ValorSistema
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono.
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set terminosMarina.
     *
     * @param string $terminosMarina
     *
     * @return ValorSistema
     */
    public function setTerminosMarina($terminosMarina)
    {
        $this->terminosMarina = $terminosMarina;

        return $this;
    }

    /**
     * Get terminosMarina.
     *
     * @return string
     */
    public function getTerminosMarina()
    {
        return $this->terminosMarina;
    }

    /**
     * Set terminosAstillero.
     *
     * @param string $terminosAstillero
     *
     * @return ValorSistema
     */
    public function setTerminosAstillero($terminosAstillero)
    {
        $this->terminosAstillero = $terminosAstillero;

        return $this;
    }

    /**
     * Get terminosAstillero.
     *
     * @return string
     */
    public function getTerminosAstillero()
    {
        return $this->terminosAstillero;
    }

    /**
     * Set correo.
     *
     * @param string $correo
     *
     * @return ValorSistema
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo.
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set folioCombustible.
     *
     * @param int $folioCombustible
     *
     * @return ValorSistema
     */
    public function setFolioCombustible($folioCombustible)
    {
        $this->folioCombustible = $folioCombustible;

        return $this;
    }

    /**
     * Get folioCombustible.
     *
     * @return int
     */
    public function getFolioCombustible()
    {
        return $this->folioCombustible;
    }

    /**
     * Set diasHabilesCombustible.
     *
     * @param int $diasHabilesCombustible
     *
     * @return ValorSistema
     */
    public function setDiasHabilesCombustible($diasHabilesCombustible)
    {
        $this->diasHabilesCombustible = $diasHabilesCombustible;

        return $this;
    }

    /**
     * Get diasHabilesCombustible.
     *
     * @return int
     */
    public function getDiasHabilesCombustible()
    {
        return $this->diasHabilesCombustible;
    }

    /**
     * Set folioSolicitud.
     *
     * @param int $folioSolicitud
     *
     * @return ValorSistema
     */
    public function setFolioSolicitud($folioSolicitud)
    {
        $this->folioSolicitud = $folioSolicitud;

        return $this;
    }

    /**
     * Get folioSolicitud.
     *
     * @return int
     */
    public function getFolioSolicitud()
    {
        return $this->folioSolicitud;
    }

    /**
     * Set folioCompra.
     *
     * @param int $folioCompra
     *
     * @return ValorSistema
     */
    public function setFolioCompra($folioCompra)
    {
        $this->folioCompra = $folioCompra;

        return $this;
    }

    /**
     * Get folioCompra.
     *
     * @return int
     */
    public function getFolioCompra()
    {
        return $this->folioCompra;
    }
}
