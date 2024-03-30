<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanAddProductToCart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
                         ->post('/cart', ['product_id' => $product->id]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('cart_product', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    public function testUserCanRemoveProductFromCart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)
             ->post('/cart', ['product_id' => $product->id]);

        $response = $this->actingAs($user)
                         ->delete("/cart/{$product->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('cart_product', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    public function testUserCanViewCart()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get('/cart');

        $response->assertStatus(200);
    }
}
