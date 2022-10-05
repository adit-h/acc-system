<?php

namespace Database\Seeders;

use App\Models\MasterAccount;
use Illuminate\Database\Seeder;

class MasterAccountTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            [
                'code' => '1000',
                'name' => 'Kas',
                'category_id' => 1,
                'status' => 'active',
            ],
            [
                'code' => '1001',
                'name' => 'Bank BCA no 1',
                'category_id' => 1,
                'status' => 'active',
            ],
            [
                'code' => '1002',
                'name' => 'Bank BCA no 2',
                'category_id' => 1,
                'status' => 'active',
            ],
            [
                'code' => '1003',
                'name' => 'Piutang Usaha',
                'category_id' => 1,
                'status' => 'active',
            ],
            [
                'code' => '1004',
                'name' => 'Piutang Karyawan',
                'category_id' => 1,
                'status' => 'active',
            ],
            [
                'code' => '2000',
                'name' => 'Persediaan',
                'category_id' => 2,
                'status' => 'active',
            ],
            [
                'code' => '2001',
                'name' => 'Pajak dibayar dimuka',
                'category_id' => 2,
                'status' => 'active',
            ],
            [
                'code' => '2002',
                'name' => 'Asuransi dibayar dimuka',
                'category_id' => 2,
                'status' => 'active',
            ],
            [
                'code' => '2003',
                'name' => 'Sewa dibayar dimuka',
                'category_id' => 2,
                'status' => 'active',
            ],
            [
                'code' => '2004',
                'name' => 'Simpanan Stabilisasi',
                'category_id' => 2,
                'status' => 'active',
            ],
            [
                'code' => '3001',
                'name' => 'Peralatan/Inventaris',
                'category_id' => 3,
                'status' => 'active',
            ],
            [
                'code' => '3002',
                'name' => 'Gedung/Kantor',
                'category_id' => 3,
                'status' => 'active',
            ],
            [
                'code' => '3003',
                'name' => 'Akum. Penyusutan Peralatan',
                'category_id' => 3,
                'status' => 'active',
            ],
            [
                'code' => '3004',
                'name' => 'Akum. Penyusutan Bangunan',
                'category_id' => 3,
                'status' => 'active',
            ],
            [
                'code' => '4000',
                'name' => 'Hutang Komersial Bahan Baku',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'code' => '4001',
                'name' => 'Hutang Komersial Operasional',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'code' => '4002',
                'name' => 'Hutang Komersial Bank',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'code' => '4003',
                'name' => 'Hutang Modal',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'code' => '4004',
                'name' => 'Hutang Asuransi',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'code' => '4005',
                'name' => 'Hutang Pajak',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'code' => '4006',
                'name' => 'Hutang Deviden',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'code' => '4007',
                'name' => 'Biaya yang masih harus dibayar',
                'category_id' => 4,
                'status' => 'active',
            ],
            [
                'code' => '5000',
                'name' => 'Modal Disetor',
                'category_id' => 5,
                'status' => 'active',
            ],
            [
                'code' => '5001',
                'name' => 'Modal Inventaris',
                'category_id' => 5,
                'status' => 'active',
            ],
            [
                'code' => '5002',
                'name' => 'Modal Investasi',
                'category_id' => 5,
                'status' => 'active',
            ],
            [
                'code' => '5003',
                'name' => 'Laba/Rugi ditahan',
                'category_id' => 5,
                'status' => 'active',
            ],
            [
                'code' => '5004',
                'name' => 'Dana Cadangan',
                'category_id' => 5,
                'status' => 'active',
            ],
            [
                'code' => '5005',
                'name' => 'Dana Resiko',
                'category_id' => 5,
                'status' => 'active',
            ],
            [
                'code' => '5006',
                'name' => 'Laba/Rugi Tahun Berjalan',
                'category_id' => 5,
                'status' => 'active',
            ],
            [
                'code' => '6000',
                'name' => 'Penjualan Bruto',
                'category_id' => 6,
                'status' => 'active',
            ],
            [
                'code' => '6001',
                'name' => 'Potongan Penjualan',
                'category_id' => 6,
                'status' => 'active',
            ],
            [
                'code' => '6002',
                'name' => 'Retur Penjualan',
                'category_id' => 6,
                'status' => 'active',
            ],
            [
                'code' => '7000',
                'name' => 'Persediaan Awal',
                'category_id' => 7,
                'status' => 'active',
            ],
            [
                'code' => '7001',
                'name' => 'Pembelian Bersih Total',
                'category_id' => 7,
                'status' => 'active',
            ],
            [
                'code' => '7002',
                'name' => 'Persediaan Akhir',
                'category_id' => 7,
                'status' => 'active',
            ],
            [
                'code' => '8001',
                'name' => 'Kredit Nota Bulanan',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8002',
                'name' => 'Biaya Advertorial',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8003',
                'name' => 'Gaji Karyawan Pemasaran',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8004',
                'name' => 'Biaya Perjalanan - UC',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8005',
                'name' => 'Biaya Ekspedisi/Pengiriman',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8006',
                'name' => 'Pemasaran',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8007',
                'name' => 'Biaya Premi Asuransi',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8008',
                'name' => 'Biaya Keamanan',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8009',
                'name' => 'Biaya Gaji Karyawan ADM',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8010',
                'name' => 'Biaya Adm Materai dan Prangko',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8011',
                'name' => 'Biaya Adm Fotokopi dan Print Out',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8012',
                'name' => 'Biaya ATK/ADM',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8013',
                'name' => 'Biaya Adm Transportasi',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8014',
                'name' => 'Biaya Telepon',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8015',
                'name' => 'Biaya Sewa/Kontrak Kantor',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8016',
                'name' => 'Biaya Listrik',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8017',
                'name' => 'Biaya Penyusutan Bangunan',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8018',
                'name' => 'Biaya Penyusutan Peralatan',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8019',
                'name' => 'Biaya Adm Lain-lain',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8020',
                'name' => 'Rawat Kendaraan',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8021',
                'name' => 'STNK',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8022',
                'name' => 'Asuransi Kendaraan',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8023',
                'name' => 'BPJS Kesehatan Team Sales',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8024',
                'name' => 'BPJS Ketenagakerjaan Team Sales',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8025',
                'name' => 'ATK/ADM',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8026',
                'name' => 'BPJS Kesehatan Adm',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8027',
                'name' => 'BPJS Ketenagakerjaan Adm',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8028',
                'name' => 'Expired Produk',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '8029',
                'name' => 'Embalage',
                'category_id' => 8,
                'status' => 'active',
            ],
            [
                'code' => '9001',
                'name' => 'Biaya Transfer, Adm dan Pajak Bank',
                'category_id' => 9,
                'status' => 'active',
            ],
            [
                'code' => '9002',
                'name' => 'Bunga Bank',
                'category_id' => 9,
                'status' => 'active',
            ],
            [
                'code' => '9003',
                'name' => 'Pendapatan lain-lain',
                'category_id' => 9,
                'status' => 'active',
            ],
            [
                'code' => '9004',
                'name' => 'Pasal 21',
                'category_id' => 9,
                'status' => 'active',
            ],
            [
                'code' => '9005',
                'name' => 'Pasal 25',
                'category_id' => 9,
                'status' => 'active',
            ],
            [
                'code' => '9006',
                'name' => 'PPN',
                'category_id' => 9,
                'status' => 'active',
            ],
        ];
        foreach ($accounts as $key => $value) {
            $acc = MasterAccount::create($value);
        }
    }
}
