<?php

namespace Tests\Feature;

use Tests\TestCase;

class MainPageTest extends TestCase
{
    public function test(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }
}
