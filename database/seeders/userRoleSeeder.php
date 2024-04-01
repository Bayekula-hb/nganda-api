<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class userRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_roles')->insert([
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
    }
}
