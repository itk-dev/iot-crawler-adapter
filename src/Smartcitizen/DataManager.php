<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Smartcitizen;

use App\DataManager\AbstractDataManager;
use App\Entity\Device;
use App\Entity\Measurement;
use App\Entity\Sensor;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
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

        $deviceId = $payload['uuid'];
        $device = $this->entityManager->getRepository(Device::class)->find($deviceId);
        if (null === $device) {
            $device = (new Device())
                ->setUser($user)
                ->setId($deviceId)
                ->setType(Device::SMARTCITIZEN)
                ->setName('smartcitizen '.$deviceId);
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
                    ->setName('Smartcitizen '.$sensorId);
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

        foreach ($data['sensors'] as $item) {
            $sensors[$item['name']] = array_merge(
                $item,
                [
                    'sensor_id' => $item['uuid'],
                    'value' => $item['raw_value'],
                ]
            );
        }

        return $sensors;
    }

    public function getAttributes(Measurement $measurement): ?array
    {
        $attributes = [];

        $data = $measurement->getPayload()['data'] ?? [];
        $sensors = $data['sensors'] ?? [];
        foreach ($sensors as $sensor) {
            if ($sensor['uuid'] === $measurement->getSensor()->getId()) {
                $attributes[] = [
                    'timestamp' => $data['recorded_at'],
                    $sensor['name'] => $sensor['raw_value'],
                ];
            }
        }

        return $attributes;
    }
}
