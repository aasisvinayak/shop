<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class categoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i < 11; $i++) {
            DB::table('categories')->insert(
                [
                'category_id' => str_random(50),
                'title'       => 'Cat '.$i,
                'parent_id'   => '',
                'status'      => 1,
                ]
            );
        }
    }
}
