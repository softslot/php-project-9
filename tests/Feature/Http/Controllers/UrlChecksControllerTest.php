<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class UrlChecksControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUrlCheck(): void
    {
        $url = 'https://example.com';

        $body = file_get_contents(__DIR__ . '/fixtures/example_com.html');

        Http::fake([
            $url => Http::response($body),
        ]);

        $urlId = DB::table('urls')
            ->insertGetId([
                'name' => $url,
                'created_at' => now()->toDateTimeString(),
            ]);

        $response1 = $this->post(route('url_checks.store', $urlId));
        $response1->assertRedirectToRoute('urls.show', [$urlId]);

        $response2 = $this->get(route('urls.show', $urlId));
        $response2->assertSeeText('200');
        $response2->assertSeeText('Page Title');
        $response2->assertSeeText('Page Description');
        $response2->assertSeeText('Page H1');
    }

    public function testErrorStatus(): void
    {
        $url = 'https://domain.com';

        Http::fake([
            $url => Http::response('content', 403),
        ]);

        $urlId = DB::table('urls')
            ->insertGetId([
                'name' => $url,
                'created_at' => now()->toDateTimeString(),
            ]);

        $response1 = $this->post(route('url_checks.store', $urlId));
        $response1->assertRedirectToRoute('urls.show', [$urlId]);

        $response2 = $this->get(route('urls.show', $urlId));
        $response2->assertSeeText('403');
    }

    public function testCheckNonExistentUrl(): void
    {
        $urlId = 999;
        $response1 = $this->post(route('url_checks.store', $urlId));
        $response1->assertNotFound();
    }
}
