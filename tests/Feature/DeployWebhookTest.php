<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeployWebhookTest extends TestCase
{
    public function test_deploy_webhook_unauthorized_without_secret(): void
    {
        $response = $this->postJson('/deploy-webhook');
        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    public function test_deploy_webhook_authorized_with_query_param(): void
    {
        $secret = env('DEPLOY_SECRET');
        $response = $this->getJson('/deploy-webhook?secret=' . $secret);
        
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
    }

    public function test_deploy_webhook_authorized_with_custom_header(): void
    {
        $secret = env('DEPLOY_SECRET');
        $response = $this->postJson('/deploy-webhook', [], [
            'X-Deploy-Secret' => $secret
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
    }
}
