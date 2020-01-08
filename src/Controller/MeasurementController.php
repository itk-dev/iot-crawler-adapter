<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Sensor;
use App\Loriot\DataParser\DataParserManager;
use App\Repository\MeasurementRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/measurements", name="measurement_")
 */
class MeasurementController extends ApiController
{
    /**
     * @Route("/{device}/{sensor}", name="lastest")
     */
    public function latest(Device $device, Sensor $sensor, MeasurementRepository $repository, DataParserManager $dataParserManager): Response
    {
        if ($sensor->getDevice() !== $device) {
            throw new BadRequestHttpException(sprintf('sensor %s does not belong to device %s', $sensor->getId(), $device->getId()));
        }
        $measurement = $repository->findLatestBySensor($sensor);

        if (null === $measurement) {
            throw new NotFoundHttpException();
        }

        $parser = $dataParserManager->getParser($measurement->getDataFormat());
        $data = $parser->parse($measurement->getPayload()['data']);
        $attributes = [];
        foreach ($data as $name => $value) {
            $attributes[] = [
                'timestamp' => $measurement->getTimestamp()->format(DateTimeImmutable::ATOM),
                $name => $value,
            ];
        }
        $result = [
            'attributes' => $attributes,
            'source' => $measurement->getPayload(),
        ];

        return  $this->createJsonResponse($result, ['groups' => 'measurement']);
    }
}
