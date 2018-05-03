<?php

namespace AppBundle\Entity\Cliente;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Usuario;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notificacion
 *
 * @ORM\Table(name="cliente_notificacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Cliente\NotificacionRepository")
 */
class Notificacion
{
    const AVISO_RECORDATORIO_PAGO = 1;
    const AVISO_MORATORIO = 2;
    const AVISO_SUSPENSION = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="fecha", type="datetime_immutable")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="smallint")
     */
    private $tipo;

    /**
     * @var \DateTimeImmutable
     */
    private $fechaNotificacionCobro;

    /**
     * @var string
     */
    private $folioCotizacion;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $usuario;

    /**
     * @var Cliente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="notificaciones", cascade={"persist", "remove"})
     */
    private $cliente;

    private static $tipoList = [
      Notificacion::AVISO_RECORDATORIO_PAGO => 'Recordatorio de pago',
      Notificacion::AVISO_MORATORIO => 'Aviso moratorio',
      Notificacion::AVISO_SUSPENSION => 'Aviso de suspensión',
    ];

    /**
     * Notificacion constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->fecha = new \DateTimeImmutable();
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
     * Set fecha.
     *
     * @param \DateTimeImmutable $fecha
     *
     * @return Notificacion
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTimeImmutable
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @return array
     */
    public static function getTipoList()
    {
        return self::$tipoList;
    }

    /**
     * Set tipo.
     *
     * @param int $tipo
     *
     * @return Notificacion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo.
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Get tipo name associated with const.
     *
     * @return string
     */
    public function getNamedTipo()
    {
        if (null === $this->tipo) { return null; }

        return self::$tipoList[$this->tipo];
    }

    /**
     * @param int $tipo
     * @return mixed
     */
    public static function findNamedTipo($tipo)
    {
        return self::$tipoList[$tipo];
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getFechaNotificacionCobro()
    {
        return $this->fechaNotificacionCobro;
    }

    /**
     * @param \DateTimeImmutable $fechaNotificacionCobro
     */
    public function setFechaNotificacionCobro($fechaNotificacionCobro)
    {
        $this->fechaNotificacionCobro = $fechaNotificacionCobro;
    }

    /**
     * @return string
     */
    public function getFolioCotizacion()
    {
        return $this->folioCotizacion;
    }

    /**
     * @param string $folioCotizacion
     */
    public function setFolioCotizacion($folioCotizacion)
    {
        $this->folioCotizacion = $folioCotizacion;
    }

    /**
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param Usuario $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Set cliente.
     *
     * @param Cliente|null $cliente
     *
     * @return Notificacion
     */
    public function setCliente(Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
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
}
