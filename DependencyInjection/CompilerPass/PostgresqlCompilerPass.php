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

class PostgresqlCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $connectionsConfiguration = $container->getParameter('postgresql.connections_configuration');
        if (empty($connectionsConfiguration)) {
            return;
        }

        foreach ($connectionsConfiguration as $connectionName => $connectionConfiguration) {
            $connectionAlias = $this->createConnection($container, $connectionConfiguration);

            $container->setAlias(
                "postgresql.{$connectionName}_connection",
                $connectionAlias
            );

            $container->setAlias(Client::class, $connectionAlias);
            $container->registerAliasForArgument($connectionAlias, Client::class, "{$connectionName} connection");
        }
    }

    /**
     * Create connection and return it's reference.
     *
     * @param ContainerBuilder $container
     * @param array            $configuration
     *
     * @return string
     */
    private function createConnection(
        ContainerBuilder $container,
        array $configuration
    ): string {
        ksort($configuration);
        $connectionHash = substr(md5(json_encode($configuration)), 0, 10);
        $definitionName = "postgresql.connection.$connectionHash";

        if (!$container->hasDefinition($definitionName)) {
            $definition = new Definition(Client::class, [$configuration, new Reference(LoopInterface::class)]);

            $container->setDefinition($definitionName, $definition);
        }

        return $definitionName;
    }
}
