<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('user_roles')->insert([
            [
                "nameRole" => "admin",
                "descriptionRole" => "admin",
            ],
            [
                "nameRole" => "manager",
                "descriptionRole" => "Manager",
            ],
            [
                "nameRole" => "cashier",
                "descriptionRole" => "Caissier",
            ],
            [
                "nameRole" => "banner",
                "descriptionRole" => "Bannière ",
            ],
            [
                "nameRole" => "receiver",
                "descriptionRole" => "Receveur",
            ],
        ]);

        
        DB::table('users')->insert([
            [
                'lastName' => 'admin',
                'firstName' => 'admin',
                'middleName' => 'admin',
                'gender' => 'M',
                'phoneNumber' => '+243825135297',
                'email' => 'hobedbayekula@gmail.com',
                'password' => bcrypt('secret0606'),
            ],
        ]);


        DB::table('user_role_tabs')->insert([
            [
                'user_id' => 1,
                'user_role_id' => 1,
            ],
        ]);
    }
}
