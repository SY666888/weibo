<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        //$this->call(UsersTableseeder::class);
        //$this->call(StatusesTableSeeder::class);
        $this->call(FollowersTableSeeder::class);
        Model::reguard();
    }
}
