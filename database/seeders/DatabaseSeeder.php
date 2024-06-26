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
                "imageDrink" => asset('img/beaufort.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Skol",
                "imageDrink" => asset('img/skol.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Heineken",
                "imageDrink" => asset('img/heineken.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Primus",
                "imageDrink" => asset('img/primus.jpg'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Legend",
                "imageDrink" => asset('img/legend.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Savana",
                "imageDrink" => asset('img/savana.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Leffe",
                "imageDrink" => asset('img/leffe.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Castel beer",
                "imageDrink" => asset('img/castel.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Tembo",
                "imageDrink" => asset('img/tembo.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Nkoy",
                "imageDrink" => asset('img/nkoyi.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Class",
                "imageDrink" => asset('img/class.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
            ],
            [
                "nameDrink" => "Turbo",
                "imageDrink" => asset('img/turbo.png'),
                "litrage" => 50,
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "Legende",
                "imageDrink" => asset('img/legende.png'),
                "litrage" => 50,
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "Doppel",
                "imageDrink" => asset('img/doppel.png'),
                "litrage" => 50,
                "typeDrink" => "Bière brune",
            ],
            [
                "nameDrink" => "XXL",
                "imageDrink" => asset('img/xxl.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Maltina",
                "imageDrink" => asset('img/maltina.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Fanta",
                "imageDrink" => asset('img/fanta.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Energy malt",
                "imageDrink" => asset('img/energy_malt.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Sprite",
                "imageDrink" => asset('img/sprite.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Coca-cola",
                "imageDrink" => asset('img/coca_cola.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Vital'o",
                "imageDrink" => asset('img/vital_o.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Schweppes",
                "imageDrink" => asset('img/schweppes.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
            ],
            [
                "nameDrink" => "Bavaria",
                "imageDrink" => asset('img/bavaria.png'),
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
