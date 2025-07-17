<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => 'Admin123',
            'email' => 'admin123@admin.com',
            'password' => bcrypt('123123123')
        ]);
        User::create([
        'name' => 'Programmer',
        'email' => 'programmer@admin.com',
        'password' => bcrypt('12345'),
        'divisi_id' => 10,
        'roles_id'  => 2,
        ]);
        User::create([
        'name' => 'Kadep',
        'email' => 'Kadep@admin.com',
        'password' => bcrypt('12345'),
        'divisi_id' => 10,
        'roles_id'  => 32,
        ]);
        User::create([
        'name' => 'SuperAdmin123',
        'email' => 'SuperAdmin123@admin.com',
        'password' => bcrypt('12345'),
        'divisi_id' => 10,
        'roles_id'  => 1,
        ]);
    }
}
