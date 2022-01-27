<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new User();
        $admin->first_name = 'Test';
        $admin->last_name = 'User';
        $admin->email = 'orders@orderstest.com';
        $admin->phone = '0712345678';
        $admin->address = '10-10001 Nairobi';
        $admin->password = bcrypt('password');
        $admin->save();

    }
}
