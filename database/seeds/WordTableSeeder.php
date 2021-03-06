<?php

use Illuminate\Database\Seeder;

class WordTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Coyote\Word::create(['word' => 'chuj*', 'replacement' => 'ch**']);
        \Coyote\Word::create(['word' => 'kurwa', 'replacement' => 'ku***']);
    }
}
