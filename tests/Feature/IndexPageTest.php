<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexPageTest extends TestCase
{
    public function testIndexPage(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }
}
