<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionSale;

class TransactionSaleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionSale::factory()
            ->count(100)
            ->state(new Sequence(
                ['receive_from' => 30],
                ['receive_from' => 31, 'store_to' => 30]
            ))
            ->sequence(fn ($sequence) => ['sale_id' => $sequence->index - 1])
            ->create();

    }
}
