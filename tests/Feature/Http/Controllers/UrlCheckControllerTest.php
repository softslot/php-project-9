<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class UrlCheckControllerTest extends TestCase
{
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

    /**
     * @throws \Exception
     */
    public function testUrlCheck(): void
    {
        $fixturePath = __DIR__ . '/fixtures/example_com.html';
        $body = file_get_contents($fixturePath);
        if ($body === false) {
            throw new \RuntimeException("Fixture not found: {$fixturePath}");
        }

        Http::fake([
            $this->url => Http::response($body),
        ]);

        $response = $this->post(route('urls.checks.store', $this->urlId));

        $response->assertRedirect();
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
        $response1 = $this->post(route('urls.checks.store', $urlId));

        $response1->assertNotFound();
    }
}
