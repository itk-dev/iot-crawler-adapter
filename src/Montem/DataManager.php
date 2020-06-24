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
        if (!isset($payload['device_id'], $payload['data'], $payload['published_at'])) {
            throw new InvalidArgumentException('Invalid payload');
        }

        $deviceId = $payload['device_id'];
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

        $data = array_map(static function ($value) use ($payload) {
            return [
                'value' => $value,
                'published_at' => $payload['published_at'],
            ];
        }, json_decode($payload['data'], true));
        $payload['parsed_data'] = $data;
        $sensorData = $this->getSensorData($data);

        foreach ($sensorData as $name => $data) {
            $sensorId = $this->getSensorId($deviceId, $name);
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
                ->setTimestamp(new DateTimeImmutable($payload['published_at']))
                ->setPayload($payload);
            $this->entityManager->persist($measurement);
        }

        $this->entityManager->flush();
    }

    public function getSensorId(string $deviceId, string $sensorId): string
    {
        return $deviceId.'-sensor-'.$sensorId;
    }

    private function getSensorData(array $data)
    {
        $sensors = [];

        foreach ($data as $name => $value) {
            $sensors[$name] = $value;
        }

        return $sensors;
    }

    public function getAttributes(Measurement $measurement): ?array
    {
        $attributes = [];

        $sensors = $measurement->getPayload()['parsed_data'] ?? [];
        foreach ($sensors as $name => $data) {
            $parts = explode('-sensor-', $measurement->getSensor()->getId());
            if (2 === \count($parts) && $name === $parts[1]) {
                $attributes[] = [
                    'timestamp' => $data['published_at'],
                    $name => $data['value'],
                ];
            }
        }

        return $attributes;
    }
}
