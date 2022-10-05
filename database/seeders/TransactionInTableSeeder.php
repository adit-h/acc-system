<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionIn;

class TransactionInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionIn::factory(100)->create();
    }
}
