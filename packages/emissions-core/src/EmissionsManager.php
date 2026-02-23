<?php
namespace Ceedbox\EmissionsCore;

use Ceedbox\EmissionsCore\Contracts\EmissionsProviderInterface;

class EmissionsManager
{
    public function __construct(
        protected EmissionsProviderInterface $provider
    ) {}

    public function provider(): EmissionsProviderInterface
    {
        return $this->provider;
    }
}