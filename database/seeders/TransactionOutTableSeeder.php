<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionOut;

class TransactionOutTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionOut::factory(200)->create();
    }
}
