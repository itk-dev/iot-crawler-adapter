<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DependencyInjection\Compiler;

use App\DataParser\DataParserManager;
use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $tag = 'iot_crawler_adapter.data_parser';
        $services = $container->findTaggedServiceIds($tag);
        $parsers = [];
        foreach ($services as $id => $tags) {
            // Make the service public so the manager can load it dynamically.
            $container->getDefinition($id)->setPublic(true);
            $service = $container->get($id);
            $parserId = $service->getId();
            if (isset($parsers[$parserId])) {
                throw new RuntimeException(sprintf('Duplicate parser id: %s', $parserId));
            }
            $parsers[$parserId] = $id;
        }

        $definition = $container->getDefinition(DataParserManager::class);
        $definition->setArgument('$parsers', $parsers);
    }
}
