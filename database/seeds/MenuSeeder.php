<?php

use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MenuVPSSeeder::class);
        $this->call(MenuSelectiveSeeder::class);
    }
}
