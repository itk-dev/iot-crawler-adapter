<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Serializer;

use App\Entity\Device;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DeviceNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($device, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($device, $format, $context);

        if (\in_array('links', $context['groups'] ?? [], true)) {
            $data['links'] = [
                'self' => $this->router->generate('device_show', [
                    'device' => $device->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ];

            foreach ($data['sensors'] as &$sensor) {
                $sensor['links'] = [
                    'latest_measurement' => $this->router->generate('measurement_latest', [
                        'device' => $device->getId(),
                        'sensor' => $sensor['id'],
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                    'all_measurements' => $this->router->generate('measurement_all', [
                        'device' => $device->getId(),
                        'sensor' => $sensor['id'],
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
            }
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Device;
    }
}
