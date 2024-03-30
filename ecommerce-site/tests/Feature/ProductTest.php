<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_product_can_be_created()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/product', [
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100,
            'quantity' => 10,
        ]);

        $response->assertOk();
        $this->assertCount(1, Product::all());
    }

    /** @test */
    public function a_product_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $this->post('/product', [
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100,
            'quantity' => 10,
        ]);

        $product = Product::first();

        $response = $this->patch('/product/' . $product->id, [
            'name' => 'Updated Product',
            'description' => 'This is an updated product',
            'price' => 200,
            'quantity' => 20,
        ]);

        $response->assertOk();
        $this->assertEquals('Updated Product', Product::first()->name);
    }

    /** @test */
    public function a_product_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $this->post('/product', [
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100,
            'quantity' => 10,
        ]);

        $product = Product::first();

        $response = $this->delete('/product/' . $product->id);

        $response->assertOk();
        $this->assertCount(0, Product::all());
    }
}
