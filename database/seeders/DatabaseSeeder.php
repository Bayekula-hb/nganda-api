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

        DB::table('drinks')->insert([
            [
                "nameDrink" => "Beaufort",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Skol",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Heineken",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Primus",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Legend",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Savana",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Leffe",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Castel beer",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Tembo",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Nkoy",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Class",
                "imageDrink" => "",
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Turbo",
                "imageDrink" => "",
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "Legende",
                "imageDrink" => "",
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "Doppel",
                "imageDrink" => "",
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "XXL",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Maltina",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Fanta",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Energy malt",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Sprite",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Coca-cola",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Vital'o",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Schweppes",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Bavaria",
                "imageDrink" => "",
                "typeDrink" => "Sucrée",
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
