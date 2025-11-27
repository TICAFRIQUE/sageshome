<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'developpeur',
            'phone' => '0142855584',
            'email' => 'developpeur@gmail.com',
            'email_verified_at' => null,
            'password' => Hash::make('password'),
            'avatar' => null,
            'role' => 'developpeur',
        ]);

        User::create([
            'username' => 'Client Test',
            'phone' => '0766554433',
            'email' => 'client@test.com',
            'email_verified_at' => null,
            'password' => Hash::make('password'),
            'avatar' => null,
            'role' => 'client',
        ]);
    }
}