<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $companyId = 1;

        $employees = [
            [
                'name' => 'John Doe',
                'phone_number' => '081234567890',
                'address' => 'Jl. Example 1, Jakarta',
            ],
            [
                'name' => 'Jane Smith',
                'phone_number' => '081234567891',
                'address' => 'Jl. Example 2, Jakarta',
            ],
            [
                'name' => 'Robert Brown',
                'phone_number' => '081234567892',
                'address' => 'Jl. Example 3, Jakarta',
            ],
            [
                'name' => 'Emily Davis',
                'phone_number' => '081234567893',
                'address' => 'Jl. Example 4, Jakarta',
            ],
            [
                'name' => 'Michael Johnson',
                'phone_number' => '081234567894',
                'address' => 'Jl. Example 5, Jakarta',
            ],
            [
                'name' => 'Linda Wilson',
                'phone_number' => '081234567895',
                'address' => 'Jl. Example 6, Jakarta',
            ],
            [
                'name' => 'David Martinez',
                'phone_number' => '081234567896',
                'address' => 'Jl. Example 7, Jakarta',
            ],
            [
                'name' => 'Sarah Thomas',
                'phone_number' => '081234567897',
                'address' => 'Jl. Example 8, Jakarta',
            ],
            [
                'name' => 'James White',
                'phone_number' => '081234567898',
                'address' => 'Jl. Example 9, Jakarta',
            ],
            [
                'name' => 'Patricia Harris',
                'phone_number' => '081234567899',
                'address' => 'Jl. Example 10, Jakarta',
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = Employee::create([
                'phone_number' => $employeeData['phone_number'],
                'address' => $employeeData['address'],
                'company_id' => $companyId,
                'role_id' => 3,
            ]);


            User::create([
                'email' => strtolower(str_replace(' ', '.', $employeeData['name'])) . '@example.com',
                'password' => Hash::make('password123'),
                'role_id' => 3,
                'employee_id' => $employee->id,
            ]);
        }
    }
}
