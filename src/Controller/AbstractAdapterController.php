<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAdapterController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected function badRequest(string $message = 'Invalid request', array $context = []): JsonResponse
    {
        if (null !== $this->logger) {
            $this->logger->error($message, $context);
        }

        // @see https://tools.ietf.org/html/rfc7807#page-3
        return new JsonResponse(array_filter([
            'title' => $message,
            'details' => $context['details'] ?? null,
        ]), Response::HTTP_BAD_REQUEST);
    }
}
