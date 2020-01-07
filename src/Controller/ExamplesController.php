<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/examples", name="examples_")
 */
class ExamplesController extends AbstractController
{
    /** @var false|string */
    private $resourceDir;

    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->resourceDir = realpath(__DIR__.'/../../examples/');
        $this->router = $router;
    }

    /**
     * @Route("/")
     */
    public function index()
    {
        $finder = new Finder();
        $data = [];
        /** @var \SplFileInfo $file */
        foreach ($finder->in($this->resourceDir)->name('*.json') as $file) {
            $path = preg_replace('/\.[^.]+$/', '', substr($file->getRealPath(), \strlen($this->resourceDir) + 1));
            $data[$path] = $this->router->generate('examples_resource', ['path' => $path], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{path}", requirements={"path": ".+"}, name="resource")
     */
    public function resource(string $path)
    {
        $filename = __DIR__.'/../../examples/'.$path.'.json';
        if (!is_file($filename)) {
            throw new NotFoundHttpException(sprintf('Invalid path: %s', $path));
        }

        return new BinaryFileResponse($filename, 200, [
            'content-type' => 'application/json',
        ]);
    }
}
