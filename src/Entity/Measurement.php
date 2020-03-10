<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MeasurementRepository")
 * @ORM\Table(indexes={@ORM\Index(name="idx_timestamp", columns={"timestamp"})})
 */
class Measurement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensor")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"measurement"})
     */
    private $sensor;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"measurement"})
     */
    private $sequenceNumber;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Groups({"measurement"})
     */
    private $timestamp;

    /**
     * @ORM\Column(type="json_array")
     * @Groups({"measurement"})
     */
    private $payload = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dataFormat;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSensor(): ?Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): self
    {
        $this->sensor = $sensor;

        return $this;
    }

    public function getSequenceNumber(): ?int
    {
        return $this->sequenceNumber;
    }

    public function setSequenceNumber(int $sequenceNumber): self
    {
        $this->sequenceNumber = $sequenceNumber;

        return $this;
    }

    public function getTimestamp(): ?DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getDataFormat(): ?string
    {
        return $this->dataFormat;
    }

    public function setDataFormat(string $dataFormat): self
    {
        $this->dataFormat = $dataFormat;

        return $this;
    }
}
