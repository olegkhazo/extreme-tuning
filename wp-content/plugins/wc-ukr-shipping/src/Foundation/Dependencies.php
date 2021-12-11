<?php

namespace kirillbdev\WCUkrShipping\Foundation;

use kirillbdev\WCUkrShipping\Contracts\Customer\CustomerStorageInterface;
use kirillbdev\WCUkrShipping\Includes\Customer\LoggedCustomerStorage;
use kirillbdev\WCUkrShipping\Includes\Customer\SessionCustomerStorage;
use kirillbdev\WCUkrShipping\Modules\Core\Activator;
use kirillbdev\WCUSCore\DB\Migrator;

if ( ! defined('ABSPATH')) {
    exit;
}

final class Dependencies
{
    public static function all()
    {
        return [
            // Contracts
            CustomerStorageInterface::class => function ($container) {
                $customerId = wc()->customer->get_id();

                return $container->make($customerId ? LoggedCustomerStorage::class : SessionCustomerStorage::class);
            },
            // Modules
            Activator::class => function ($container) {
                return new Activator($container->make(Migrator::class));
            }
        ];
    }
}