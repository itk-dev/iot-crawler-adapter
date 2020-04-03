<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Montem;

use App\DataManager\AbstractDataManager;
use App\Entity\Device;
use App\Entity\Measurement;
use App\Entity\Sensor;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DataManager extends AbstractDataManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function handle(array $payload)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        // @TODO Check payload
        if (!isset($payload['id'], $payload['data'])) {
            throw new InvalidArgumentException('Invalid payload');
        }

        $deviceId = $payload['id'];
        $device = $this->entityManager->getRepository(Device::class)->find($deviceId);
        if (null === $device) {
            $device = (new Device())
                ->setUser($user)
                ->setId($deviceId)
                ->setType(Device::MONTEM)
                ->setName(Device::MONTEM.' '.$deviceId);
            $this->entityManager->persist($device);
        }

        if ($device->getUser() !== $user) {
            throw new RuntimeException('Device not owned by current user');
        }

        $sensorData = $this->getSensorData($payload['data']);

        foreach ($sensorData as $name => $data) {
            $sensorId = $this->getSensorId($deviceId, $data['sensor_id']);
            $sensor = $this->entityManager->getRepository(Sensor::class)->find($sensorId);
            if (null === $sensor) {
                $sensor = (new Sensor())
                    ->setId($sensorId)
                    ->setDevice($device)
                    ->setName(Device::MONTEM.' '.$sensorId);
                $this->entityManager->persist($sensor);
            }

            $measurement = (new Measurement())
                ->setSensor($sensor)
                ->setSequenceNumber($data['measurement_id'])
                ->setTimestamp(new DateTimeImmutable($payload['data']['recorded_at']))
                ->setPayload($payload);
            $this->entityManager->persist($measurement);
        }

        $this->entityManager->flush();
    }

    public function getSensorId(string $deviceId, string $sensorId): string
    {
        return $sensorId;
    }

    private function getSensorData(array $data)
    {
        $sensors = [];

        // @TODO

        return $sensors;
    }

    public function getAttributes(Measurement $measurement): ?array
    {
        $attributes = [];

        // @TODO

        return $attributes;
    }
}
