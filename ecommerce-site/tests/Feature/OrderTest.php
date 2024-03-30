<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Order;
use App\User;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_an_order()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $response = $this->post('/orders', [
            'user_id' => $user->id,
            'total' => 100,
        ]);

        $response->assertOk();
        $this->assertCount(1, Order::all());
    }

    /** @test */
    public function a_user_can_view_an_order()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $order = factory(Order::class)->create(['user_id' => $user->id]);

        $response = $this->get('/orders/' . $order->id);

        $response->assertSee($order->total);
    }
}
