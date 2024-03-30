<?php

use Illuminate\Database\Seeder;
use App\Order;
use App\User;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            factory(Order::class, 2)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}