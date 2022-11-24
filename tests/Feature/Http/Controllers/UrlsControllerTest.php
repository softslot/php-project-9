<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testHomePageStatus(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeText('Анализатор страниц');
    }

    public function testUrlsPageStatus(): void
    {
        $response = $this->get(route('urls.index'));

        $response->assertOk();
        $response->assertSeeText('Последняя проверка');
    }

    public function testAddUrl(): void
    {
        $response1 = $this->post(
            route('urls.store'),
            ['url' => ['name' => 'https://ru.hexlet.io/']]
        );
        $response1->assertRedirect(route('urls.show', 1));

        $response2 = $this->get(route('urls.show', 1));
        $response2->assertSeeText('Сайт: https://ru.hexlet.io');
    }

    public function testAddTwoIdenticalUrl(): void
    {
        $response1 = $this->post(
            route('urls.store'),
            ['url' => ['name' => 'https://ru.hexlet.io/']]
        );
        $response1->assertRedirect(route('urls.show', 1));

        $response2 = $this->post(
            route('urls.store'),
            ['url' => ['name' => 'https://ru.hexlet.io/homepage']]
        );
        $response2->assertRedirect(route('urls.show', 1));
    }

    public function test404(): void
    {
        $response = $this->get(route('urls.show', 999));

        $response->assertNotFound();
    }
}
