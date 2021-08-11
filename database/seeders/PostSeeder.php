<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::create(['name'=>'Bài đăng 1']);
        Post::create(['name'=>'Bài đăng 2']);
        Post::create(['name'=>'Bài đăng 3']);
        Post::create(['name'=>'Bài đăng 4']);
        Post::create(['name'=>'Bài đăng 5']);
        Post::create(['name'=>'Bài đăng 6']);
        Post::create(['name'=>'Bài đăng 7']);
        Post::create(['name'=>'Bài đăng 8']);
    }
}
