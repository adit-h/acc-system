<?php

namespace Database\Seeders;

use App\Models\TransLimit;
use Illuminate\Database\Seeder;

class TransLimitTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $setting = [
            [
                'date_start' => date('Y-1-1'),
                'date_end' => date('Y-12-t'),
                'status' => 1,
            ],
        ];
        foreach ($setting as $key => $value) {
            $transLimit = TransLimit::create($value);
        }
    }
}
