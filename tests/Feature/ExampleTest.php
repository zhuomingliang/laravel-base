<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase {
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example() {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function permission_test() {
        $user = \App\Models\User::first();

        $this->actingAs($user, 'admin');

        $this->getJson('/permission')->assertJsonStructure();

        $this->getJson('/permission/detail?id=xxx')->assertStatus(204);

        $this->postJson('/permission', [
            'id'    => 1,
            'pg_id' => 1,
            'cname' => 'Permission',
            'name' => 'account',
            'guard_name' => 'admin',
            'icon' => 'icon-user',
            'sequence' => 1,
            'description' => 'permission'
        ])->assertStatus(409);

        $this->delete('/permission?id=xxx')->assertStatus(422);
    }
}
