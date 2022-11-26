<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionIn;

class TransactionInTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionIn::factory(200)->create();
    }
}
