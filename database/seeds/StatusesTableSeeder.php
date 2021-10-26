<?php

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_ids = ['1','2','3'];
        $faker = app(Faker\Generator::class);//们通过 app() 方法来获取一个 Faker 的实例
        $statuses = factory(Status::class)->times(100)->make()->each(function ($statu
 $status->user_id = $faker->randomElement($user_ids);
 });
Status::insert($statuses->toArray());

    }
}
