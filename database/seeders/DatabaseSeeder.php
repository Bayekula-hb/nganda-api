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
            [
                "nameRole" => "store-manager",
                "descriptionRole" => "Store manager",
            ],
            [
                "nameRole" => "supervisor",
                "descriptionRole" => "Superviseur",
            ],
        ]);

        DB::table('drinks')->insert([
            [
                "nameDrink" => "Beaufort petit",
                "imageDrink" => asset('img/beaufort.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Beaufort Grand",
                "imageDrink" => asset('img/beaufort.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Skol",
                "imageDrink" => asset('img/skol.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Heineken Petit",
                "imageDrink" => asset('img/heineken.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Heineken Grand",
                "imageDrink" => asset('img/heineken.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Primus petit",
                "imageDrink" => asset('img/primus.jpg'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Primus Victoire",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Primus Grand",
                "imageDrink" => asset('img/primus.jpg'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Legend Petit",
                "imageDrink" => asset('img/legend.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Savana",
                "imageDrink" => asset('img/savana.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Leffe",
                "imageDrink" => asset('img/leffe.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Castel beer",
                "imageDrink" => asset('img/castel.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Tembo",
                "imageDrink" => asset('img/tembo.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Nkoy",
                "imageDrink" => asset('img/nkoyi.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Class",
                "imageDrink" => asset('img/class.png'),
                "litrage" => 50,
                "typeDrink" => "Bière normal",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Turbo King Pt",
                "imageDrink" => asset('img/turbo.png'),
                "litrage" => 50,
                "typeDrink" => "Bière brune",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Turbo King Grand",
                "imageDrink" => asset('img/turbo.png'),
                "litrage" => 50,
                "typeDrink" => "Bière brune",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Legende Grand",
                "imageDrink" => asset('img/legende.png'),
                "litrage" => 50,
                "typeDrink" => "Bière brune",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Legende petit",
                "imageDrink" => asset('img/legende.png'),
                "litrage" => 50,
                "typeDrink" => "Bière brune",
                "priorityDrink" => 0,
                "numberBottle" => 24
            ],
            [
                "nameDrink" => "Doppel",
                "imageDrink" => asset('img/doppel.png'),
                "litrage" => 50,
                "typeDrink" => "Bière brune",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "XXL",
                "imageDrink" => asset('img/xxl.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Maltina",
                "imageDrink" => asset('img/maltina.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Fanta Petit",
                "imageDrink" => asset('img/fanta.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Fanta Grand",
                "imageDrink" => asset('img/fanta.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Energy malt",
                "imageDrink" => asset('img/energy_malt.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Sprite",
                "imageDrink" => asset('img/sprite.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Coca-cola Petit",
                "imageDrink" => asset('img/coca_cola.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Coca-cola Grand",
                "imageDrink" => asset('img/coca_cola.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Top Grand",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Vital'o",
                "imageDrink" => asset('img/vital_o.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Schweppes",
                "imageDrink" => asset('img/schweppes.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Bavaria",
                "imageDrink" => asset('img/bavaria.png'),
                "litrage" => 50,
                "typeDrink" => "Sucrée",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Red bull",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucré",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Exo",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Sucré",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Bouteille d'eau",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Eau",
                "priorityDrink" => 0,
                "numberBottle" => 12,
            ],
            [
                "nameDrink" => "Mutzing",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Boisson",
                "priorityDrink" => 0,
                "numberBottle" => 24,
            ],
            [
                "nameDrink" => "Likofi",
                "imageDrink" => "",
                "litrage" => 50,
                "typeDrink" => "Boisson",
                "priorityDrink" => 0,
                "numberBottle" => 12,
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
            [
                'lastName' => 'serge',
                'firstName' => 'kashala',
                'middleName' => 'jacobit',
                'userName' => 'jacobitkashala@gmail.com',
                'gender' => 'M',
                'phoneNumber' => '+243825135297',
                'email' => 'jacobitkashala@gmail.com',
                'password' => bcrypt('jacobitkashala@'),
            ],
        ]);


        DB::table('user_role_tabs')->insert([
            [
                'user_id' => 1,
                'user_role_id' => 1,
            ],
            [
                'user_id' => 2,
                'user_role_id' => 1,
            ],
        ]);

        DB::table('establishments')->insert([
            [
                'nameEtablishment' => "Nganda Bar App",
                'address' => "Nganda Bar 243, Kinshasa/Congo",
                'workers' => [1, 2],
                'subscriptionExpiryDate' => "2080-12-31",
                'user_id' => 1,
            ],
        ]);

    }
}
