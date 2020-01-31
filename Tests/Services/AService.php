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

namespace Drift\Postgresql\Tests\Services;

use PgAsync\Client;

class AService
{
    /**
     * @var Client
     */
    private $connection1;

    /**
     * @var Client
     */
    private $connection2;

    /**
     * @var Client
     */
    private $connection3;

    /**
     * AService constructor.
     *
     * @param Client $connection1
     * @param Client $connection2
     * @param Client $connection3
     */
    public function __construct(Client $usersConnection, Client $ordersConnection, Client $users2Connection)
    {
        $this->connection1 = $usersConnection;
        $this->connection2 = $ordersConnection;
        $this->connection3 = $users2Connection;
    }

    /**
     * Are equal.
     */
    public function areOK(): bool
    {
        return $this->connection1 !== $this->connection2
            && $this->connection1 === $this->connection3;
    }
}
