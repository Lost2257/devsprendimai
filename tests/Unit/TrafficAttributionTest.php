<?php

namespace Tests\Unit;

use App\Support\TrafficAttribution;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class TrafficAttributionTest extends TestCase
{
    public function test_it_identifies_search_social_and_direct_sources(): void
    {
        $google = Request::create('https://devsprendimai.lt/', 'GET', [], [], [], [
            'HTTP_REFERER' => 'https://www.google.com/search?q=svetainiu+kurimas',
        ]);
        $instagram = Request::create('https://devsprendimai.lt/', 'GET', [], [], [], [
            'HTTP_REFERER' => 'https://l.instagram.com/some/path',
        ]);
        $direct = Request::create('https://devsprendimai.lt/', 'GET');

        $this->assertSame(['Google', 'organic'], array_values(array_intersect_key(TrafficAttribution::from($google), array_flip(['source', 'medium']))));
        $this->assertSame('Instagram', TrafficAttribution::from($instagram)['source']);
        $this->assertSame('Tiesiogiai', TrafficAttribution::from($direct)['source']);
        $this->assertSame('https://www.google.com', TrafficAttribution::from($google)['referrer']);
    }

    public function test_utm_values_take_priority_over_referrer(): void
    {
        $request = Request::create(
            'https://devsprendimai.lt/?utm_source=newsletter&utm_medium=email&utm_campaign=spalis',
            'GET',
            [],
            [],
            [],
            ['HTTP_REFERER' => 'https://www.google.com/'],
        );

        $attribution = TrafficAttribution::from($request);

        $this->assertSame('newsletter', $attribution['source']);
        $this->assertSame('email', $attribution['medium']);
        $this->assertSame('spalis', $attribution['campaign']);
    }

    public function test_it_classifies_devices_and_browsers(): void
    {
        $this->assertSame('Telefonas', TrafficAttribution::device('Mozilla/5.0 (iPhone) AppleWebKit Safari/605.1'));
        $this->assertSame('Planšetė', TrafficAttribution::device('Mozilla/5.0 (iPad) AppleWebKit Safari/605.1'));
        $this->assertSame('Chrome', TrafficAttribution::browser('Mozilla/5.0 Chrome/130.0.0.0 Safari/537.36'));
    }

    public function test_referrer_lookalikes_and_array_utm_values_are_not_trusted(): void
    {
        $request = Request::create('https://devsprendimai.lt/?utm_source[]=private&utm_campaign[]=private', 'GET', [], [], [], [
            'HTTP_REFERER' => 'https://facebook.com.attacker.test/private?email=secret',
        ]);
        $attribution = TrafficAttribution::from($request);
        $this->assertSame('referral', $attribution['medium']);
        $this->assertSame('facebook.com.attacker.test', $attribution['source']);
        $this->assertNull($attribution['campaign']);
        $this->assertSame('https://facebook.com.attacker.test', $attribution['referrer']);
    }
}
