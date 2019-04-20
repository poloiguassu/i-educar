<?php

use Illuminate\Database\Seeder;

class CitySchoolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/city-schools-table.sql')
        );
    }
}
