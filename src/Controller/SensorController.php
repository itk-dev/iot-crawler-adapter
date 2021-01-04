<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Entity\Sensor;
use App\Repository\SensorRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/sensors", name="sensor_")
 */
class SensorController extends ApiController
{
    /**
     * @Route("", name="index")
     */
    public function index(Request $request, SensorRepository $repository, NormalizerInterface $normalizer): Response
    {
        $criteria = [];
        if ($type = $request->get('type')) {
            $criteria['type'] = $type;
        }

        $sensors = $repository->findBy($criteria);
        $data = $normalizer->normalize($sensors, 'json', ['groups' => ['sensor', 'links']]);

        return new JsonResponse($data);
    }

    /**
     * @Route("/{sensor}", name="show")
     */
    public function show(Sensor $sensor, NormalizerInterface $normalizer): Response
    {
        $data = $normalizer->normalize($sensor, 'json', ['groups' => ['sensor', 'links']]);

        return new JsonResponse($data);
    }
}
