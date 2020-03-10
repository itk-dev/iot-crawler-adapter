<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Smartcitizen\DataManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/smartcitizen", name="smartcitizen_")
 */
class SmartcitizenController
{
    /**
     * @Route("", name="post", methods={"POST"})
     * @IsGranted("ROLE_SMARTCITIZEN")
     */
    public function post(Request $request, SerializerInterface $serializer, DataManager $dataManager)
    {
        try {
            $content = $request->getContent();
            $contentType = $request->headers->get('content-type');

            if (0 === strpos($contentType, 'application/json')) {
                try {
                    $payload = $serializer->decode($content, 'json');
                    if (isset($payload['id'], $payload['uuid'], $payload['data'])) {
                        $dataManager->handle($payload);

                        return new JsonResponse('created', Response::HTTP_CREATED);
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