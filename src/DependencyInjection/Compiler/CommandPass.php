<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CommandPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $service = $container->findDefinition(Application::class);
        $availableCommands = array_keys($container->findTaggedServiceIds('console.command'));

        foreach ($availableCommands as $command) {
            $service->addMethodCall('add', [new Reference($command)]);
        }
    }
}