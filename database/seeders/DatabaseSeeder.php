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
                "nameRole" => "barman",
                "descriptionRole" => "Barman ",
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
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Skol",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Heineken",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Primus",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Legend",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Savana",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Leffe",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Castel beer",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Tembo",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Nkoy",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Class",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Turbo",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "Legende",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "Doppel",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "XXL",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Maltina",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Fanta",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Energy malt",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Sprite",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Coca-cola",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Vital'o",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Schweppes",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Bavaria",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
        ]);

        
        DB::table('users')->insert([
            [
                'lastName' => 'admin',
                'firstName' => 'admin',
                'middleName' => 'admin',
                'userName' => 'hobedbayekula@gmail.com',
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
