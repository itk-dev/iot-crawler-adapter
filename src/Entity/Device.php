<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device implements TimestampableInterface
{
    public const LORIOT = 'loriot';
    public const MONTEM = 'montem';
    public const SMARTCITIZEN = 'smartcitizen';

    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=64)
     * @Groups("device")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups("device")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("device")
     */
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $metadata = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sensor", mappedBy="device", orphanRemoval=true)
     * @Groups("device")
     */
    private $sensors;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="devices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->sensors = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return Collection|Sensor[]
     */
    public function getSensors(): Collection
    {
        return $this->sensors;
    }

    public function addSensor(Sensor $sensor): self
    {
        if (!$this->sensors->contains($sensor)) {
            $this->sensors[] = $sensor;
            $sensor->setDevice($this);
        }

        return $this;
    }

    public function removeSensor(Sensor $sensor): self
    {
        if ($this->sensors->contains($sensor)) {
            $this->sensors->removeElement($sensor);
            // set the owning side to null (unless already changed)
            if ($sensor->getDevice() === $this) {
                $sensor->setDevice(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @Groups("device")
     */
    public function getLocation(): ?array
    {
        return $this->metadata['data']['location'] ?? null;
    }
}
