<?php

/*
 * This file is part of the Drift Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Drift\Postgresql\DependencyInjection\CompilerPass;

use PgAsync\Client;
use React\EventLoop\LoopInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PostgresqlCompilerPass.
 */
class PostgresqlCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $clientsConfiguration = $container->getParameter('postgresql.clients_configuration');
        if (empty($clientsConfiguration)) {
            return;
        }

        foreach ($clientsConfiguration as $clientName => $clientConfiguration) {
            static::createclient(
                $container,
                $clientName,
                $clientConfiguration
            );
        }
    }

    /**
     * Create client and return it's reference.
     *
     * @param ContainerBuilder $container
     * @param string           $clientName
     * @param array            $configuration
     */
    public static function createclient(
        ContainerBuilder $container,
        string $clientName,
        array $configuration
    ) {
        $definitionName = "postgresql.{$clientName}_client";

        $definition = new Definition(
            Client::class,
            [
                $configuration,
                new Reference(LoopInterface::class),
            ]
        );

        $container->setDefinition($definitionName, $definition);
        $container->setAlias(Client::class, $definitionName);
        $container->registerAliasForArgument($definitionName, Client::class, "{$clientName} client");
    }
}
