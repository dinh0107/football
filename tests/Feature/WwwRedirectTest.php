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

    public function test_index_php_redirects_to_root_with_301(): void
    {
        $response = $this->get('/index.php');

        $response->assertStatus(301);
        $this->assertEquals(url('/'), $response->headers->get('Location'));
    }

    public function test_index_html_redirects_to_root_with_301(): void
    {
        $response = $this->get('/index.html');

        $response->assertStatus(301);
        $this->assertEquals(url('/'), $response->headers->get('Location'));
    }

    public function test_server_request_uri_index_php_redirects_to_root(): void
    {
        $response = $this->withServerVariables([
            'REQUEST_URI' => '/index.php',
            'SCRIPT_NAME' => '/index.php',
            'PHP_SELF' => '/index.php',
            'HTTP_HOST' => 'tysongoaihang.site',
        ])->get('/index.php');

        $response->assertStatus(301);
        $this->assertEquals(url('/'), $response->headers->get('Location'));
    }

    public function test_server_request_uri_index_php_with_query_string(): void
    {
        $response = $this->withServerVariables([
            'REQUEST_URI' => '/index.php?date=2026-10-23',
            'SCRIPT_NAME' => '/index.php',
            'QUERY_STRING' => 'date=2026-10-23',
            'HTTP_HOST' => 'tysongoaihang.site',
        ])->get('/index.php?date=2026-10-23');

        $response->assertStatus(301);
        $this->assertEquals(url('/?date=2026-10-23'), $response->headers->get('Location'));
    }

    public function test_server_request_uri_index_php_with_subpath(): void
    {
        $response = $this->withServerVariables([
            'REQUEST_URI' => '/index.php/bang-xep-hang',
            'SCRIPT_NAME' => '/index.php',
            'HTTP_HOST' => 'tysongoaihang.site',
        ])->get('/index.php/bang-xep-hang');

        $response->assertStatus(301);
        $this->assertEquals(url('/bang-xep-hang'), $response->headers->get('Location'));
    }
}
