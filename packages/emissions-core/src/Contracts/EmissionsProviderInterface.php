<?php
namespace Ceedbox\EmissionsCore\Contracts;

    // @TODO Make the Interface more agnostic
interface EmissionsProviderInterface
{
    public function dashboardUrl(string $clientHandle): string;
    public function emissionsUrl(string $clientHandle, string $emissionsId): string;
}
