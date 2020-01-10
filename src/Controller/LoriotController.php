<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Loriot\DataManager;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/loriot", name="loriot_")
 */
class LoriotController
{
    /**
     * @Route("", name="post", methods={"POST"})
     */
    public function post(Request $request, SerializerInterface $serializer, DataManager $dataManager)
    {
        try {
            $dataPath = $request->get('dataPath', 'data');
            $dataFormat = $request->get('dataFormat');
            if (empty($dataFormat)) {
                return $this->badRequest('Missing data format');
            }

            $content = $request->getContent();
            $contentType = $request->headers->get('content-type');

            if (0 === strpos($contentType, 'application/json')) {
                try {
                    $payload = $serializer->decode($content, 'json');

                    if (isset($payload['EUI'], $payload['cmd'])) {
                        if ('gw' === $payload['cmd']) {
                            // Actual data.
                            $dataManager->handle($payload, $dataPath, $dataFormat);

                            return new JsonResponse('created', Response::HTTP_CREATED);
                        } else {
                            // Handshake or similar.
                            return new JsonResponse('ok', Response::HTTP_OK);
                        }
                    }
                } catch (UnexpectedValueException $exception) {
                    return $this->badRequest($exception->getMessage());
                }
            } else {
                return $this->badRequest(sprintf('Invalid content-type: %s', $contentType));
            }
        } catch (Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }

        return $this->badRequest();
    }

    private function badRequest(string $message = 'Invalid request', string $details = null): JsonResponse
    {
        // @see https://tools.ietf.org/html/rfc7807#page-3
        return new JsonResponse(array_filter([
            'title' => $message,
            'details' => $details,
        ]), Response::HTTP_BAD_REQUEST);
    }
}
