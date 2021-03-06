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

namespace Drift\Postgresql\Tests;

use Drift\Postgresql\PostgresqlBundle;
use Drift\Postgresql\Tests\Services\AService;
use Mmoreram\BaseBundle\Kernel\DriftBaseKernel;
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AutowiringTest.
 */
class AutowiringTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new DriftBaseKernel([
            FrameworkBundle::class,
            PostgresqlBundle::class,
        ], [
            'parameters' => [
                'kernel.secret' => 'sdhjshjkds',
            ],
            'framework' => [
                'test' => true,
            ],
            'imports' => [
                ['resource' => __DIR__.'/autowiring.yml'],
            ],
            'services' => [
                'reactphp.event_loop' => [
                    'class' => LoopInterface::class,
                    'factory' => [
                        Factory::class,
                        'create',
                    ],
                ],
            ],
            'postgresql' => [
                'clients' => [
                    'users' => [
                        'host' => '127.0.0.1',
                        'database' => 'orders',
                        'user' => 'mmoreram',
                        'password' => 'secret',
                    ],
                    'orders' => [
                        'host' => '127.0.0.2',
                        'database' => 'orders',
                        'user' => 'mmoreram',
                        'password' => 'secret',
                    ],
                    'users2' => [
                        'host' => '127.0.0.1',
                        'port' => 5432,
                        'database' => 'orders',
                        'user' => 'mmoreram',
                        'password' => 'secret',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test.
     */
    public function testProperClient()
    {
        $client = static::get(AService::class);
        $this->assertTrue($client->areOk());
    }
}
