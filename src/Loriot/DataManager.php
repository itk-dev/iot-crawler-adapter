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
use Symfony\Component\PropertyAccess\PropertyAccess;
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

    public function handle(array $payload, string $dataPath, string $dataFormat)
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
        $data = $this->getData($payload, $dataPath);
        $data = $parser->parse($data, true);
        $sensorData = $this->getSensorData($data);

        foreach ($sensorData as $name => $data) {
            $sensorId = $this->getSensorId($deviceId, $data['sensor_id']);
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

    public function getSensorId(string $deviceId, string $measurement): string
    {
        return $deviceId.'-sensor-'.$measurement;
    }

    public function getMeasurementName(string $sensorId)
    {
        if (preg_match('/^(?P<deviceId>.+)-sensor-(?P<measurement>.+)$/', $sensorId, $matches)) {
            return $matches['measurement'];
        }

        return null;
    }

    private function getData(array $payload, string $path)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $path = '['.implode('][', explode('.', $path)).']';

        return $accessor->getValue($payload, $path);
    }

    private function getSensorData(array $data)
    {
        $sensors = [];

        // Assume that wa have data with two keys per sensor:
        //  «measurement»_sensor_id
        //  «measurement»_value
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
