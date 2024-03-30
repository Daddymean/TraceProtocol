<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/home');

        $this->assertCount(1, User::all());
    }

    /** @test */
    public function a_user_can_login()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/home');

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_logout()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->be($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}