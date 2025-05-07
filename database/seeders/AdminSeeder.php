<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::updateOrCreate([
            'phone' => '01014001055',
        ], [
            'name' => 'Shokry Mansour',
            'email' => 'shokrymansor123@gmail.com',
            'phone' => '01014001055',
            'password' => Hash::make('123456789'),
            'gender' => 'male',
        ]);        
    }
}
