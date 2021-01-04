<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/", name="default_")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route(name="index")
     */
    public function index(UrlHelper $urlHelper, array $indexData): Response
    {
        if (isset($indexData['routes'])) {
            $indexData['routes'] = array_map(function ($route) use ($urlHelper) {
                $params = [];
                if (\is_array($route)) {
                    [$route, $params] = [$route['name'], $route['params'] ?? []];
                }

                return $this->generateUrl($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
            }, $indexData['routes']);
        }

        return new JsonResponse($indexData);
    }
}
