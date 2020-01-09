<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Loriot;

use App\Entity\Device;
use App\Entity\Measurement;
use App\Entity\Sensor;
use App\Loriot\DataParser\DataParserManager;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DataManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DataParserManager */
    private $dataParserManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, DataParserManager $dataParserManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->dataParserManager = $dataParserManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function handle(array $payload, string $dataFormat)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $deviceId = $payload['EUI'];
        $device = $this->entityManager->getRepository(Device::class)->find($deviceId);
        if (null === $device) {
            $device = (new Device())
                ->setUser($user)
                ->setId($deviceId)
                ->setName('Device '.$deviceId);
            $this->entityManager->persist($device);
        }

        if ($device->getUser() !== $user) {
            throw new RuntimeException('Device not owned by current user');
        }

        $parser = $this->dataParserManager->getParser($dataFormat);
        $data = $parser->parse($payload['data'], true);
        $sensors = $this->getSensorData($data);

        foreach ($sensors as $name => $data) {
            $sensorId = $deviceId.'-sensor-'.$data['sensor_id'];
            $sensor = $this->entityManager->getRepository(Sensor::class)->find($sensorId);
            if (null === $sensor) {
                $sensor = (new Sensor())
                    ->setId($sensorId)
                    ->setDevice($device)
                    ->setName('Sensor '.$sensorId);
                $this->entityManager->persist($sensor);
            }

            $measurement = (new Measurement())
                ->setSensor($sensor)
                ->setSequenceNumber($payload['seqno'])
                ->setTimestamp($this->getTimestamp($payload['ts']))
                ->setDataFormat($dataFormat)
                ->setPayload($payload);
            $this->entityManager->persist($measurement);
        }

        $this->entityManager->flush();
    }

    private function getSensorData(array $data)
    {
        $sensors = [];

        foreach ($data as $key => $value) {
            if (preg_match('/^(?P<name>.+)_(?P<key>sensor_id|value)$/', $key, $matches)) {
                $sensors[$matches['name']][$matches['key']] = $value;
            }
        }

        if (empty($sensors)) {
            // Assume that we have sensor name => value data.
            foreach ($data as $name => $value) {
                $sensors[$name] = [
                    'sensor_id' => $name,
                    'value' => $value,
                ];
            }
        }

        return $sensors;
    }

    private function getTimestamp(int $timestamp): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat('U.u', sprintf('%d.%d', $timestamp / 1000, $timestamp % 1000));
    }
}
