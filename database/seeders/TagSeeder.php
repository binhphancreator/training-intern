<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::create(['name'=>'Tag 1']);
        Tag::create(['name'=>'Tag 2']);
        Tag::create(['name'=>'Tag 3']);
        Tag::create(['name'=>'Tag 4']);
        Tag::create(['name'=>'Tag 5']);
        Tag::create(['name'=>'Tag 6']);
        Tag::create(['name'=>'Tag 7']);
        Tag::create(['name'=>'Tag 8']);

        DB::table('taggables')->insert(['tag_id'=>1,'taggable_id'=>1,'taggable_type'=>'video']);
        DB::table('taggables')->insert(['tag_id'=>1,'taggable_id'=>1,'taggable_type'=>'post']);
        DB::table('taggables')->insert(['tag_id'=>1,'taggable_id'=>2,'taggable_type'=>'video']);
        DB::table('taggables')->insert(['tag_id'=>1,'taggable_id'=>2,'taggable_type'=>'post']);
        DB::table('taggables')->insert(['tag_id'=>3,'taggable_id'=>3,'taggable_type'=>'video']);
        DB::table('taggables')->insert(['tag_id'=>3,'taggable_id'=>3,'taggable_type'=>'post']);
        DB::table('taggables')->insert(['tag_id'=>2,'taggable_id'=>4,'taggable_type'=>'video']);
        DB::table('taggables')->insert(['tag_id'=>2,'taggable_id'=>5,'taggable_type'=>'video']);
    }
}
