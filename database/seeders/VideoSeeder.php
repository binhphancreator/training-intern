<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Video::create(['name'=>'Video 1']);
        Video::create(['name'=>'Video 2']);
        Video::create(['name'=>'Video 3']);
        Video::create(['name'=>'Video 4']);
        Video::create(['name'=>'Video 5']);
        Video::create(['name'=>'Video 6']);
        Video::create(['name'=>'Video 7']);
        Video::create(['name'=>'Video 8']);
    }
}
