<?php
namespace Ceedbox\EmissionsCore\Contracts;

interface EmissionsProviderInterface
{
    public function dashboardUrl(string $clientHandle): string;
    public function emissionsUrl(string $clientHandle, string $emissionsId): string;
}
