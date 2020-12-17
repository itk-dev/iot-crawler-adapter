<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SensorRepository")
 */
class Sensor
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=132)
     * @Groups({"sensor", "device"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Device", inversedBy="sensors")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"sensor"})
     */
    private $device;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $metadata = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(?Device $device): self
    {
        $this->device = $device;

        return $this;
    }

    /**
     * @Groups({"sensor", "device"})
     */
    public function getName(): ?string
    {
        return $this->metadata['name'] ?? $this->name;
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
     * @Groups({"sensor", "device"})
     */
    public function getDescription(): ?string
    {
        return $this->metadata['description'] ?? null;
    }

    /**
     * @Groups({"sensor", "device"})
     */
    public function getUnit(): ?string
    {
        return $this->metadata['unit'] ?? null;
    }
}
