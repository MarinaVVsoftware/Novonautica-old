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
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="expiration", type="datetime")
     */
    private $expiration;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $usedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="used_at", type="datetime", nullable=true)
     */
    private $usedAt;

    public function __construct($pinCode)
    {
        $this->pin = $pinCode;
        $this->expiration = new \DateTime('+24 hours');
        $this->status = true;
        $this->createdAt = new \DateTime();
        $this->description = 'Pincode demo';
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
            'description' => $this->description
        ];
    }

    /**
     * Set pin.
     *
     * @param string $pin
     *
     * @return Pincode
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Pincode
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set expiration.
     *
     * @param \DateTime $expiration
     *
     * @return Pincode
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return Pincode
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Pincode
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set usedAt.
     *
     * @param \DateTime|null $usedAt
     *
     * @return Pincode
     */
    public function setUsedAt($usedAt = null)
    {
        $this->usedAt = $usedAt;

        return $this;
    }

    /**
     * Get usedAt.
     *
     * @return \DateTime|null
     */
    public function getUsedAt()
    {
        return $this->usedAt;
    }

    /**
     * Set createdBy.
     *
     * @param \AppBundle\Entity\Usuario|null $createdBy
     *
     * @return Pincode
     */
    public function setCreatedBy(\AppBundle\Entity\Usuario $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return \AppBundle\Entity\Usuario|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set usedBy.
     *
     * @param \AppBundle\Entity\Usuario|null $usedBy
     *
     * @return Pincode
     */
    public function setUsedBy(\AppBundle\Entity\Usuario $usedBy = null)
    {
        $this->usedBy = $usedBy;

        return $this;
    }

    /**
     * Get usedBy.
     *
     * @return \AppBundle\Entity\Usuario|null
     */
    public function getUsedBy()
    {
        return $this->usedBy;
    }
}
