<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class UrlChecksControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $url;
    private int $urlId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = 'https://example.com';
        $this->urlId = DB::table('urls')
            ->insertGetId([
                'name' => $this->url,
                'created_at' => now()->toDateTimeString(),
            ]);
    }

    public function testUrlCheck(): void
    {
        $body = file_get_contents(__DIR__ . '/fixtures/example_com.html');

        Http::fake([
            $this->url => Http::response($body),
        ]);

        $response = $this->post(route('url_checks.store', $this->urlId));
        $response->assertRedirect();
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('url_checks', [
            'url_id' => $this->urlId,
            'status_code' => 200,
            'h1' => 'Page H1',
            'title' => 'Page Title',
            'description' => 'Page Description',
        ]);
    }

    public function testCheckNonExistentUrl(): void
    {
        $urlId = 999;
        $response1 = $this->post(route('url_checks.store', $urlId));
        $response1->assertNotFound();
    }
}
