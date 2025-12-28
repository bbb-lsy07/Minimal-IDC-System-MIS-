<?php

declare(strict_types=1);

interface ProviderInterface
{
    public function createService(array $product, array $user, array $order): array;

    public function suspendService(array $service): void;

    public function unsuspendService(array $service): void;

    public function terminateService(array $service): void;

    public function getServiceStatus(array $service): array;
}
