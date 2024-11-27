<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanyAndManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $company = Company::create([
            'name' => 'PT. Example',
            'email' => 'info@example.com',
            'phone_number' => '02112345678',
        ]);

        User::create([
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2,
            'employee_id' => null,
        ]);
    }
}