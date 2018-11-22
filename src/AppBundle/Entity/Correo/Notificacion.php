<?php

namespace AppBundle\Entity\Correo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notificacion
 *
 * @ORM\Table(name="correo_notificacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Correo\NotificacionRepository")
 */
class Notificacion
{
    const EVENTO_CREAR = 1;
    const EVENTO_VALIDAR = 2;
    const EVENTO_ACEPTAR = 3;
    const EVENTO_EDITAR = 4;

    const TIPO_MARINA = 1;
    const TIPO_ASTILLERO = 2;
    const TIPO_ODT = 3;
    const TIPO_COMBUSTIBLE = 4;
    const TIPO_SOLICITUD = 5;
    const TIPO_COMPRA = 6;
    const TIPO_ALMACEN = 7;

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
     * @ORM\Column(name="correo", type="string", length=100)
     */
    private $correo;

    /**
     * @var int
     *
     * @ORM\Column(name="evento", type="smallint")
     */
    private $evento;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="smallint")
     */
    private $tipo;

    private static $eventoList = [
        Notificacion::EVENTO_CREAR => 'Nueva',
        Notificacion::EVENTO_VALIDAR => 'Validar',
        Notificacion::EVENTO_ACEPTAR => 'Aceptar',
        Notificacion::EVENTO_EDITAR => 'Editar'
    ];

    private static $tipoList = [
        Notificacion::TIPO_MARINA => 'Marina',
        Notificacion::TIPO_ASTILLERO => 'Astillero',
        Notificacion::TIPO_ODT => 'ODT',
        Notificacion::TIPO_COMBUSTIBLE => 'Combustible',
        Notificacion::TIPO_SOLICITUD => 'Solicitud',
        Notificacion::TIPO_COMPRA => 'Compra',
        Notificacion::TIPO_ALMACEN => 'Almacén'
    ];

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
     * Set correo.
     *
     * @param string $correo
     *
     * @return Notificacion
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
     * @return int
     */
    public function getEvento()
    {
        if (null === $this->evento) { return null; }
        return $this->evento;
    }

    /**
     * @return int
     */
    public function getEventoNombre()
    {
        if (null === $this->evento) { return null; }
        return self::$eventoList[$this->evento];
    }

    /**
     * @param int $evento
     */
    public function setEvento($evento)
    {
        $this->evento = $evento;
    }

    public static function getEventoList()
    {
        return self::$eventoList;
    }

    /**
     * @return int
     */
    public function getTipo()
    {
        if (null === $this->tipo) { return null; }
        return $this->tipo;
    }

    /**
     * @return int
     */
    public function getTipoNombre()
    {
        if (null === $this->tipo) { return null; }
        return self::$tipoList[$this->tipo];
    }

    /**
     * @param int $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public static function getTipoList()
    {
        return self::$tipoList;
    }
}
