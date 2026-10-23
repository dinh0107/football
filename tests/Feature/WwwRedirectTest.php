<?php

namespace Tests\Feature;

use Tests\TestCase;

class WwwRedirectTest extends TestCase
{
    public function test_www_redirects_to_non_www_with_301(): void
    {
        $response = $this->get('https://www.tysongoaihang.site/bang-xep-hang');

        $response->assertStatus(301);
        $this->assertEquals('https://tysongoaihang.site/bang-xep-hang', $response->headers->get('Location'));
    }

    public function test_non_www_loads_normally(): void
    {
        $response = $this->withServerVariables([
            'HTTP_HOST' => 'tysongoaihang.site',
            'HTTPS' => 'on',
        ])->get('/bang-xep-hang');

        $response->assertStatus(200);
    }
}
