<?php

use Illuminate\Database\Seeder;
use App\Cart;

class CartsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Cart::class, 50)->create();
    }
}