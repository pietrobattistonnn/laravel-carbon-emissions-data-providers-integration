<?php
use PHPUnit\Framework\TestCase;
use Ceedbox\LuneModule\LuneClient;

class LuneClientTest extends TestCase
{
    public function test_dashboard_url_contains_org_and_client()
    {
        $client = new LuneClient(
            'ORG123',
            'secret',
            'https://sustainability.lune.co'
        );

        $url = $client->dashboardUrl('CLIENT1');

        $this->assertStringContainsString('ORG123', $url);
        $this->assertStringContainsString('CLIENT1', $url);
        $this->assertStringContainsString('access_token=', $url);
    }
}
