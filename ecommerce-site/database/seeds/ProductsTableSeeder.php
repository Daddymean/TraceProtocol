<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 50; $i++) {
            DB::table('products')->insert([
                'name' => Str::random(10),
                'description' => Str::random(50),
                'price' => rand(100, 1000),
                'image' => 'images/products/default.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}