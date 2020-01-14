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
use App\Loriot\DataManager;
use App\Loriot\DataParser\DataParserManager;
use App\Repository\MeasurementRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/{device}/{sensor}", name="latest")
     */
    public function latest(Request $request, ?Device $device, ?Sensor $sensor, MeasurementRepository $repository, DataParserManager $dataParserManager, DataManager $dataManager): Response
    {
        if (null === $device) {
            return $this->createExceptionResponse(new NotFoundHttpException(sprintf('Device %s not found', $request->attributes->get('_route_params')['device'] ?? null)));
        }
        if (null === $sensor) {
            return $this->createExceptionResponse(new NotFoundHttpException(sprintf('Sensor %s not found', $request->attributes->get('_route_params')['sensor'] ?? null)));
        }
        if ($sensor->getDevice() !== $device) {
            throw new BadRequestHttpException(sprintf('sensor %s does not belong to device %s', $sensor->getId(), $device->getId()));
        }
        $measurement = $repository->findLatestBySensor($sensor);
        $measurementName = $dataManager->getMeasurementName($sensor->getId());

        if (null === $measurement) {
            throw new NotFoundHttpException();
        }

        $parser = $dataParserManager->getParser($measurement->getDataFormat());
        $data = $parser->parse($measurement->getPayload()['data']);
        $attributes = [];
        foreach ($data as $name => $value) {
            if ($measurementName === $name) {
                $attributes[] = [
                    'timestamp' => $measurement->getTimestamp()->format(DateTimeImmutable::ATOM),
                    $name => $value,
                ];
            }
        }

        if (empty($attributes)) {
            return $this->createExceptionResponse(new NotFoundHttpException(sprintf('Measurement %s not found', $measurementName)));
        }

        $result = [
            'attributes' => $attributes,
            'source' => $measurement->getPayload(),
        ];

        return  $this->createJsonResponse($result, ['groups' => 'measurement']);
    }
}
