<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionOut;

class TransactionOutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionOut::factory(100)->create();
    }
}
