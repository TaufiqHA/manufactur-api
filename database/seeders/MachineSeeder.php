<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the machines to be created with corresponding users
        $machines = [
            [
                'user_name' => 'Potong Operator',
                'user_email' => 'potong@example.com',
                'user_username' => 'potong_operator',
                'code' => 'MCH-POTONG-001',
                'name' => 'Mesin Potong',
                'type' => 'POTONG',
                'capacity_per_hour' => 100,
                'status' => 'IDLE',
                'is_maintenance' => false,
            ],
            [
                'user_name' => 'Plong Operator',
                'user_email' => 'plong@example.com',
                'user_username' => 'plong_operator',
                'code' => 'MCH-PLONG-001',
                'name' => 'Mesin Plong',
                'type' => 'PLONG',
                'capacity_per_hour' => 80,
                'status' => 'RUNNING',
                'is_maintenance' => false,
            ],
            [
                'user_name' => 'Press Operator',
                'user_email' => 'press@example.com',
                'user_username' => 'press_operator',
                'code' => 'MCH-PRESS-001',
                'name' => 'Mesin Press',
                'type' => 'PRESS',
                'capacity_per_hour' => 60,
                'status' => 'IDLE',
                'is_maintenance' => false,
            ],
            [
                'user_name' => 'Las Pen Operator',
                'user_email' => 'laspen@example.com',
                'user_username' => 'laspen_operator',
                'code' => 'MCH-LASPEN-001',
                'name' => 'Mesin Las Pen',
                'type' => 'LASPEN',
                'capacity_per_hour' => 40,
                'status' => 'MAINTENANCE',
                'is_maintenance' => true,
            ],
            [
                'user_name' => 'Las MIG Operator',
                'user_email' => 'lasmig@example.com',
                'user_username' => 'lasmig_operator',
                'code' => 'MCH-LASMIG-001',
                'name' => 'Mesin Las MIG',
                'type' => 'LAS MIG',
                'capacity_per_hour' => 50,
                'status' => 'RUNNING',
                'is_maintenance' => false,
            ],
            [
                'user_name' => 'Phosphating Operator',
                'user_email' => 'phosphating@example.com',
                'user_username' => 'phosphating_operator',
                'code' => 'MCH-PHOS-001',
                'name' => 'Mesin Phosphating',
                'type' => 'PHOSPHTING',
                'capacity_per_hour' => 30,
                'status' => 'OFFLINE',
                'is_maintenance' => false,
            ],
            [
                'user_name' => 'Cat Operator',
                'user_email' => 'cat@example.com',
                'user_username' => 'cat_operator',
                'code' => 'MCH-CAT-001',
                'name' => 'Mesin Cat',
                'type' => 'CAT',
                'capacity_per_hour' => 25,
                'status' => 'IDLE',
                'is_maintenance' => false,
            ],
            [
                'user_name' => 'Packing Operator',
                'user_email' => 'packing@example.com',
                'user_username' => 'packing_operator',
                'code' => 'MCH-PACK-001',
                'name' => 'Mesin Packing',
                'type' => 'PACKING',
                'capacity_per_hour' => 120,
                'status' => 'RUNNING',
                'is_maintenance' => false,
            ],
        ];

        // Create machines with their corresponding users
        foreach ($machines as $machineData) {
            // Find or create the user for this machine
            $user = User::firstOrCreate(
                ['email' => $machineData['user_email']],
                [
                    'name' => $machineData['user_name'],
                    'username' => $machineData['user_username'],
                    'email' => $machineData['user_email'],
                    'password' => Hash::make('password'),
                    'role' => 'operator', // Assuming there's a role field
                ]
            );

            // Create the machine for this user
            Machine::create([
                'user_id' => $user->id,
                'code' => $machineData['code'],
                'name' => $machineData['name'],
                'type' => $machineData['type'],
                'capacity_per_hour' => $machineData['capacity_per_hour'],
                'status' => $machineData['status'],
                'is_maintenance' => $machineData['is_maintenance'],
            ]);
        }
    }
}
