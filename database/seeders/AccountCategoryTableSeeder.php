<?php

namespace Database\Seeders;

use App\Models\AccountCategory;
use Illuminate\Database\Seeder;

class AccountCategoryTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Aset Lancar',
                'description' => 'Aset, Kas dan Piutang',
            ],
            [
                'name' => 'Aset tak Lancar',
                'description' => '',
            ],
            [
                'name' => 'Inventaris dan Penyusutan',
                'description' => '',
            ],
            [
                'name' => 'Hutang/Kewajiban',
                'description' => '',
            ],
            [
                'name' => 'Modal/Laba ditahan',
                'description' => '',
            ],
            [
                'name' => 'Penjualan',
                'description' => '',
            ],
            [
                'name' => 'Harga Pokok',
                'description' => '',
            ],
            [
                'name' => 'Biaya',
                'description' => '',
            ],
            [
                'name' => 'Lain-lain',
                'description' => 'Biaya Lain, Pendapatan Lain, Pajak',
            ]
        ];
        foreach ($categories as $key => $value) {
            $cat = AccountCategory::create($value);
        }
    }
}
