<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Generator $faker): void
    {
        $adminRoleId = 1;
        $userRoleId = 2;

        $NUMBER_OF_EMPLOYEES = 20;

        //create admin user
        User::create([
            'email' => 'admin@example.com',
            'name' => 'Admin',
            'role_id' => $adminRoleId,
            'password' => bcrypt('admin123')
        ]);

        $i = 0;
        while ($i < $NUMBER_OF_EMPLOYEES) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            if ($i === 0) {
                User::create([
                    'email' => 'carloeusebi@gmail.com',
                    'name' => 'Carlo Eusebi',
                    'role_id' => $userRoleId,
                    'password' => bcrypt('12345678'),
                ]);
            } else {
                User::create([
                    'email' => strtolower("$firstName.$lastName@mail.it"),
                    'name' => "$firstName $lastName",
                    'role_id' => $userRoleId,
                    'password' => bcrypt(strtolower("$firstName.$lastName")),
                ]);
            }
            $i++;
        }
    }
}
