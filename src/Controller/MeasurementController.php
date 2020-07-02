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
use App\Loriot\DataManager as LoriotDataManager;
use App\Montem\DataManager as MontemDataManager;
use App\Repository\MeasurementRepository;
use App\Repository\SensorRepository;
use App\Smartcitizen\DataManager as SmartcitizenDataManager;
use Doctrine\Common\Collections\Criteria;
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
     * @Route("/{device}", name="device")
     */
    public function device(
        ?Device $device,
        SensorRepository $repository
    ): Response {
        $sensors = $repository->findByDevice($device);

        return $this->createJsonResponse($sensors, ['groups' => 'sensor']);
    }

    /**
     * @Route("/{device}/{sensor}", name="latest")
     */
    public function latest(
        Request $request,
        ?Device $device,
        ?Sensor $sensor,
        MeasurementRepository $repository,
        LoriotDataManager $loriotDataManager,
        SmartcitizenDataManager $smartcitizenDataManager,
        MontemDataManager $montemDataManager
    ): Response {
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

        if (null === $measurement) {
            throw new NotFoundHttpException();
        }
        $type = $measurement->getSensor()->getDevice()->getType();
        switch ($type) {
            case Device::LORIOT:
                $attributes = $loriotDataManager->getAttributes($measurement);
                break;

            case Device::SMARTCITIZEN:
                $attributes = $smartcitizenDataManager->getAttributes($measurement);
                break;

            case Device::MONTEM:
                $attributes = $montemDataManager->getAttributes($measurement);
                break;
        }

        if (empty($attributes)) {
            throw new NotFoundHttpException();
        }

        $result = [
            'attributes' => $attributes,
            'source' => $measurement->getPayload(),
        ];

        return $this->createJsonResponse($result, ['groups' => 'measurement']);
    }

    /**
     * @Route("/{device}/{sensor}/all", name="all")
     */
    public function all(
        Request $request,
        Device $device,
        Sensor $sensor,
        MeasurementRepository $repository
    ): Response {
        $result = $repository->findBy([
            'sensor' => $sensor,
        ], [
            'timestamp' => Criteria::DESC,
        ]);

        return $this->createJsonResponse($result, ['groups' => ['sensor', 'measurement']]);
    }
}
