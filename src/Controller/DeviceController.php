<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Repository\DeviceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/devices", name="device_")
 */
class DeviceController extends ApiController
{
    /**
     * @Route("", name="index")
     */
    public function index(DeviceRepository $repository, SerializerInterface $serializer): Response
    {
        $devices = $repository->findAll();

        return $this->createJsonResponse($devices, ['groups' => 'device']);
    }
}
