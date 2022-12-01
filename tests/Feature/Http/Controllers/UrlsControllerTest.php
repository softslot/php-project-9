<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlsControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $url;
    private int $urlId;

    public function setUp(): void
    {
        parent::setUp();

        $this->url = 'https://ru.hexlet.io';
        $data = [
            'name' => $this->url,
            'created_at' => now()->toDateTimeString(),
        ];

        $this->urlId = DB::table('urls')->insertGetId($data);
    }

    public function testHomePage(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function testUrlsPage(): void
    {
        $response = $this->get(route('urls.index'));

        $response->assertOk();
    }

    public function testShowPage(): void
    {
        $response = $this->get(route('urls.show', $this->urlId));

        $response->assertOk();
    }

    public function testStoreUrl(): void
    {
        $data = ['name' => 'https://google.com'];
        $response = $this->post(
            route('urls.store'),
            ['url' => $data]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('urls', $data);
    }

    public function testStoreTwoIdenticalUrl(): void
    {
        $response = $this->post(
            route('urls.store'),
            ['url' => ['name' => $this->url]]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('urls', 1);
    }

    public function testInvalidUrl(): void
    {
        $response = $this->post(
            route('urls.store'),
            ['url' => ['name' => 'qwerty']]
        );

        $response->assertStatus(422);
    }

    public function testNotFound404(): void
    {
        $urlId = 999;
        $response = $this->get(route('urls.show', $urlId));

        $response->assertNotFound();
    }
}
