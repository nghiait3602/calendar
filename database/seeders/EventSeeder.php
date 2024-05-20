<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('events')->insert([
            "name_event" => "Nguyen Trung Nghia",
            "notes" => "nghia@gmail.com",
            "start_time" => "2024-05-14T10:00:00",
            "end_time" => "2024-05-14T10:00:00",
            "user_id" => "1",
            "categoris_id" => "1"
        ]);
    }
}
