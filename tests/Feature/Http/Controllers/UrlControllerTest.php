<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    private string $url;
    private int $urlId;

    public function setUp(): void
    {
        parent::setUp();

        $this->url = 'https://ru.hexlet.io';
        $this->urlId = DB::table('urls')
            ->insertGetId([
                'name' => $this->url,
                'created_at' => now()->toDateTimeString(),
            ]);
    }

    public function testIndexPage(): void
    {
        $response = $this->get(route('urls.index'));

        $response->assertOk();
    }

    public function testShowPage(): void
    {
        $response = $this->get(route('urls.show', $this->urlId));

        $response->assertOk();
    }

    public function testStore(): void
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

    public function testNotFoundPage(): void
    {
        $urlId = 999;
        $response = $this->get(route('urls.show', $urlId));

        $response->assertNotFound();
    }
}
