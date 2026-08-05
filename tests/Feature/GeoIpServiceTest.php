<?php

namespace Tests\Feature;

use App\Services\GeoIpService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeoIpServiceTest extends TestCase
{
    public function test_returns_null_and_makes_no_request_for_a_private_ip(): void
    {
        Http::fake();

        $result = (new GeoIpService)->guessCountryCode('127.0.0.1');

        $this->assertNull($result);
        Http::assertNothingSent();
    }

    public function test_returns_country_code_on_successful_lookup(): void
    {
        Http::fake([
            'ip-api.com/*' => Http::response(['status' => 'success', 'countryCode' => 'MK']),
        ]);

        $result = (new GeoIpService)->guessCountryCode('8.8.8.8');

        $this->assertSame('MK', $result);
    }

    public function test_returns_null_when_the_api_call_fails(): void
    {
        Http::fake([
            'ip-api.com/*' => Http::response(null, 500),
        ]);

        $result = (new GeoIpService)->guessCountryCode('8.8.8.8');

        $this->assertNull($result);
    }

    public function test_returns_null_when_the_api_times_out_instead_of_throwing(): void
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('timed out');
        });

        $result = (new GeoIpService)->guessCountryCode('8.8.8.8');

        $this->assertNull($result);
    }

    public function test_caches_the_result_so_repeated_lookups_do_not_hit_the_api_again(): void
    {
        Http::fake([
            'ip-api.com/*' => Http::response(['status' => 'success', 'countryCode' => 'MK']),
        ]);

        $service = new GeoIpService;
        $service->guessCountryCode('8.8.8.8');
        $service->guessCountryCode('8.8.8.8');

        Http::assertSentCount(1);
    }
}
