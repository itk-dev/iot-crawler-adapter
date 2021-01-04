<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Serializer;

use App\Entity\Sensor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SensorNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($sensor, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($sensor, $format, $context);

        if (\in_array('links', (array) $context['groups'] ?? [], true)) {
            $device = $sensor->getDevice();
            $data['links'] = [
                'self' => $this->router->generate('sensor_show', [
                    'sensor' => $sensor->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'latest_measurement' => $this->router->generate('measurement_latest', [
                    'device' => $device->getId(),
                    'sensor' => $sensor->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'all_measurements' => $this->router->generate('measurement_all', [
                    'device' => $device->getId(),
                    'sensor' => $sensor->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ];
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Sensor;
    }
}
