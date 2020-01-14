<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController
{
    /** @var SerializerInterface */
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    protected function createJsonResponse($data, $context = []): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($data, 'json', $context),
            Response::HTTP_OK,
            [],
            true
        );
    }

    protected function createExceptionResponse(HttpExceptionInterface $exception)
    {
        return new JsonResponse(
            [
                'title' => $exception->getMessage() ?: Response::$statusTexts[$exception->getStatusCode()],
            ],
            $exception->getStatusCode()
        );
    }
}
