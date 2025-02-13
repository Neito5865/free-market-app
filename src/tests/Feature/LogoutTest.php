<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Middleware\VerifyCsrfToken;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post('/login', $data);

        $this->assertTrue(Auth::check());

        $this->assertEquals($user->id, Auth::id());

        $response = $this->post('/logout');

        $this->assertFalse(Auth::check());

        $response->assertRedirect('/');
    }
}
