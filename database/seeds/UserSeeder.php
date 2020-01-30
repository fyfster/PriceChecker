<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $dbStructureJson = file_get_contents(__DIR__ ."/databaseSeed.json");
        $dbStructureArray = json_decode($dbStructureJson, true);

        if (isset($dbStructureArray['users'])) {
            $users = $dbStructureArray['users'];
            foreach ($users as $user) {
                $userId = DB::table('users')->insertGetId([
                    'username' => $user['username'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'salt' => hash('sha256', $user['password']),
                    'uuid' => $user['uuid'],
                    'city_id' => $user['city_id'],
                    'address' => $user['address'],
                    'phone' => rand(10000000, 99999999),
                    'activated_at' => date("Y-m-d H:i:s")
                ]);
            }

        }

    }

}
?>
