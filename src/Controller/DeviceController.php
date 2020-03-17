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
use App\Repository\DeviceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/devices", name="device_")
 */
class DeviceController extends ApiController
{
    /**
     * @Route("", name="index")
     */
    public function index(DeviceRepository $repository, NormalizerInterface $normalizer): Response
    {
        $devices = $repository->findAll();
        $data = $normalizer->normalize($devices, 'json', ['groups' => ['device', 'links']]);

        return new JsonResponse($data);
    }

    /**
     * @Route("/{device}", name="show")
     */
    public function show(Device $device, NormalizerInterface $normalizer): Response
    {
        $data = $normalizer->normalize($device, 'json', ['groups' => ['device', 'links']]);

        return new JsonResponse($data);
    }
}
