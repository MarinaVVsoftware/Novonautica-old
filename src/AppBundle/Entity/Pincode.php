<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pincode
 *
 * @ORM\Table(name="pincode")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PincodeRepository")
 */
class Pincode implements \JsonSerializable
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
     * @ORM\Column(name="pin", type="string", length=8, unique=true)
     */
    private $pin;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="expiration", type="datetime")
     */
    private $expiration;

    public function __construct($pinCode)
    {
        $this->pin = $pinCode;
        $this->expiration = new \DateTime('+5 minutes');
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
     * Get pin.
     *
     * @return int
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'pin' => $this->pin,
            'expiration' => $this->expiration->format(\DateTime::ATOM),
        ];
    }
}
