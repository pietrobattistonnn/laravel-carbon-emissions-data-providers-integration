<?php

namespace Ceedbox\LuneModule;

use Firebase\JWT\JWT;
use Ceedbox\EmissionsCore\Contracts\EmissionsProviderInterface;

final class LuneClient implements EmissionsProviderInterface
{
    private const ALG = 'HS256';

    public function __construct(
        private string $orgId,
        private string $apiKey,
        private string $baseUrl,
        private int $ttl = 3600
    ) {}

    private function token(string $clientHandle): string
    {
        $now = time();

        return JWT::encode([
            'iat' => $now,
            'exp' => $now + $this->ttl,
            'scope' => ['handles' => [$clientHandle]],
        ], $this->apiKey, self::ALG);
    }

    public function dashboardUrl(string $clientHandle): string
    {
        return sprintf(
            '%s/logistics/%s/%s?access_token=%s',
            rtrim($this->baseUrl, '/'),
            $this->orgId,
            $clientHandle,
            $this->token($clientHandle)
        );
    }

    public function emissionsUrl(string $clientHandle, string $emissionsId): string
    {
        return sprintf(
            '%s/logistics/%s/%s/emissions/%s?access_token=%s',
            rtrim($this->baseUrl, '/'),
            $this->orgId,
            $clientHandle,
            $emissionsId,
            $this->token($clientHandle)
        );
    }
}