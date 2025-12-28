<?php

declare(strict_types=1);

require_once MIS_ROOT . '/modules/providers/ProviderInterface.php';

final class MoFangProvider implements ProviderInterface
{
    public function __construct(private array $config)
    {
    }

    public function createService(array $product, array $user, array $order): array
    {
        throw new RuntimeException('MoFang provider not implemented.');
    }

    public function suspendService(array $service): void
    {
        throw new RuntimeException('MoFang provider not implemented.');
    }

    public function unsuspendService(array $service): void
    {
        throw new RuntimeException('MoFang provider not implemented.');
    }

    public function terminateService(array $service): void
    {
        throw new RuntimeException('MoFang provider not implemented.');
    }

    public function getServiceStatus(array $service): array
    {
        throw new RuntimeException('MoFang provider not implemented.');
    }
}
