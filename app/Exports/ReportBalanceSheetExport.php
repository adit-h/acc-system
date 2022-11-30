<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\MasterAccount;
use App\Models\TransactionIn;
//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportBalanceSheetExport implements FromView, WithColumnWidths, WithStyles
{
    //use Exportable;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        // test
        $data = TransactionIn::query()->with('receiveFrom')->with('storeTo')
            ->whereMonth('trans_date', $this->month)
            ->whereYear('trans_date', $this->year);

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 32,
            'C' => 18,
            'D' => 18,
            'E' => 18,
            'F' => 18
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Set align center
            1    => ['alignment' => ['center']],
            2    => ['alignment' => ['center']],
            2    => ['font' => ['bold' => true]],
            'C'  => ['alignment' => ['right']],
            'D'  => ['alignment' => ['right']],
            'E'  => ['alignment' => ['right']],
            'F'  => ['alignment' => ['right']],
        ];
    }

    public function view(): View
    {
        $trans_in = $trans_out = $main_price = [];
        $trans_in_prev = $trans_out_prev = $master = [];    // init
        $in_data1 = $in_data2 = $in_data3 = [];
        $out_data1 = $out_data2 = [];
        $bucket = $bucket_prev = [];
        $filter = $filter_prev = '';
        $in_cat = [1, 2, 3];
        $out_cat = [4, 5];

        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));
        if ($this->month > 1) {
            $prev_month = $this->month - 1;
            $prev_year = $this->year;
        } else {
            $prev_month = 12;
            $prev_year = $this->year - 1;
        }

        // Query current Month Transactions
        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));
        $trans_in = DB::table('transaction_in AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $this->year)
                ->whereMonth('t.trans_date', '=', $this->month);
            $trans_sale = DB::table('transaction_sale AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $this->year)
                ->whereMonth('t.trans_date', '=', $this->month);
            $trans = DB::table('transaction_out AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $this->year)
                ->whereMonth('t.trans_date', '=', $this->month)
                ->union($trans_sale)
                ->union($trans_in)
                ->orderBy('trans_date')
                ->get();

        // prev month
        $filter_prev = date('F Y', strtotime($prev_year.'-'.$prev_month.'-01'));
        $trans_in_prev = DB::table('transaction_in AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $prev_year)
            ->whereMonth('t.trans_date', '=', $prev_month);
        $trans_sale_prev = DB::table('transaction_sale AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $prev_year)
            ->whereMonth('t.trans_date', '=', $prev_month);
        $trans_prev = DB::table('transaction_out AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->whereYear('t.trans_date', '=', $prev_year)
            ->whereMonth('t.trans_date', '=', $prev_month)
            ->union($trans_in_prev)
            ->union($trans_sale_prev)
            ->orderBy('trans_date')
            ->get();

        $bucket_prev = $this->initMasterContainer(null);
        // calculate previous month transactions
        foreach ($trans_prev as $key => $t) {
            $bucket_prev[$t->fromId]['debet'] += $t->value;
            $bucket_prev[$t->toId]['kredit'] += $t->value;
        }
        // Aktiva 1. filter master account with account id = 1
        $in_data1 = $this->initMasterContainer(1);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in_data1)) {
                //$in_data1[$t->toId]['balance'] += $t->value;
                $in_data1[$t->toId]['kredit'] += $t->value;
                $in_data1[$t->toId]['balance'] = $in_data1[$t->toId]['last_balance'] + $in_data1[$t->toId]['debet'] - $in_data1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data1)) {
                $in_data1[$t->fromId]['debet'] += $t->value;
                $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['last_balance'] + $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data1) && array_key_exists($t->fromId, $in_data1))  {
                $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['last_balance'] + $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in_data1)) {
                //$in_data1[$t->toId]['balance'] += $t->value;
                $in_data1[$t->toId]['kredit'] += $t->value;
                $in_data1[$t->toId]['balance'] = $in_data1[$t->toId]['last_balance'] + $in_data1[$t->toId]['debet'] - $in_data1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data1)) {
                $in_data1[$t->fromId]['debet'] += $t->value;
                $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['last_balance'] + $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data1) && array_key_exists($t->fromId, $in_data1))  {
                $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['last_balance'] + $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
        }

        // Aktiva 2. filter master account with account id = 2
        $in_data2 = $this->initMasterContainer(2);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in_data2)) {
                //$in_data2[$t->toId]['balance'] += $t->value;
                $in_data2[$t->toId]['kredit'] += $t->value;
                $in_data2[$t->toId]['balance'] = $in_data2[$t->toId]['last_balance'] + $in_data2[$t->toId]['debet'] - $in_data2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data2)) {
                $in_data2[$t->fromId]['debet'] += $t->value;
                $in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['last_balance'] + $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data2) && array_key_exists($t->fromId, $in_data2))  {
                $in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['last_balance'] + $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in_data2)) {
                //$in_data2[$t->toId]['balance'] += $t->value;
                $in_data2[$t->toId]['kredit'] += $t->value;
                $in_data2[$t->toId]['balance'] = $in_data2[$t->toId]['last_balance'] + $in_data2[$t->toId]['debet'] - $in_data2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data2)) {
                $in_data2[$t->fromId]['debet'] += $t->value;
                $in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['last_balance'] + $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data2) && array_key_exists($t->fromId, $in_data2))  {
                $in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['last_balance'] + $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }
        }
        // Aktiva 3. filter master account with account id = 3
        $in_data3 = $this->initMasterContainer(3);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in_data3)) {
                //$in_data3[$t->toId]['balance'] += $t->value;
                $in_data3[$t->toId]['kredit'] += $t->value;
                $in_data3[$t->toId]['balance'] = $in_data3[$t->toId]['last_balance'] + $in_data3[$t->toId]['debet'] - $in_data3[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data3)) {
                $in_data3[$t->fromId]['debet'] += $t->value;
                $in_data3[$t->fromId]['balance'] = $in_data3[$t->fromId]['last_balance'] + $in_data3[$t->fromId]['debet'] - $in_data3[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data3) && array_key_exists($t->fromId, $in_data3))  {
                $in_data3[$t->fromId]['balance'] = $in_data3[$t->fromId]['last_balance'] + $in_data3[$t->fromId]['debet'] - $in_data3[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in_data3)) {
                //$in_data3[$t->toId]['balance'] += $t->value;
                $in_data3[$t->toId]['kredit'] += $t->value;
                $in_data3[$t->toId]['balance'] = $in_data3[$t->toId]['last_balance'] + $in_data3[$t->toId]['debet'] - $in_data3[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data3)) {
                $in_data3[$t->fromId]['debet'] += $t->value;
                $in_data3[$t->fromId]['balance'] = $in_data3[$t->fromId]['last_balance'] + $in_data3[$t->fromId]['debet'] - $in_data3[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data3) && array_key_exists($t->fromId, $in_data3))  {
                $in_data3[$t->fromId]['balance'] = $in_data3[$t->fromId]['last_balance'] + $in_data3[$t->fromId]['debet'] - $in_data3[$t->fromId]['kredit'];
            }
        }

        // Pasiva 1. filter master account with account id = 4
        $out_data1 = $this->initMasterContainer(4);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $out_data1)) {
                //$out_data1[$t->toId]['balance'] += $t->value;
                $out_data1[$t->toId]['kredit'] += $t->value;
                $out_data1[$t->toId]['balance'] = $out_data1[$t->toId]['last_balance'] + $out_data1[$t->toId]['debet'] - $out_data1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data1)) {
                $out_data1[$t->fromId]['debet'] += $t->value;
                $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['last_balance'] + $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data1) && array_key_exists($t->fromId, $out_data1))  {
                $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['last_balance'] + $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $out_data1)) {
                //$out_data1[$t->toId]['balance'] += $t->value;
                $out_data1[$t->toId]['kredit'] += $t->value;
                $out_data1[$t->toId]['balance'] = $out_data1[$t->toId]['last_balance'] + $out_data1[$t->toId]['debet'] - $out_data1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data1)) {
                $out_data1[$t->fromId]['debet'] += $t->value;
                $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['last_balance'] + $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data1) && array_key_exists($t->fromId, $out_data1))  {
                $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['last_balance'] + $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
        }
        // Pasiva 2. filter master account with account id = 5
        $out_data2 = $this->initMasterContainer(5);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $out_data2)) {
                //$out_data2[$t->toId]['balance'] += $t->value;
                $out_data2[$t->toId]['kredit'] += $t->value;
                $out_data2[$t->toId]['balance'] = $out_data2[$t->toId]['last_balance'] + $out_data2[$t->toId]['debet'] - $out_data2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data2)) {
                $out_data2[$t->fromId]['debet'] += $t->value;
                $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['last_balance'] + $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data2) && array_key_exists($t->fromId, $out_data2))  {
                $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['last_balance'] + $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $out_data2)) {
                //$out_data2[$t->toId]['balance'] += $t->value;
                $out_data2[$t->toId]['kredit'] += $t->value;
                $out_data2[$t->toId]['balance'] = $out_data2[$t->toId]['last_balance'] + $out_data2[$t->toId]['debet'] - $out_data2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data2)) {
                $out_data2[$t->fromId]['debet'] += $t->value;
                $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['last_balance'] + $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data2) && array_key_exists($t->fromId, $out_data2))  {
                $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['last_balance'] + $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
        }

        return view('reports.export-balanceSheet', compact('in_data1', 'in_data2', 'in_data3', 'out_data1', 'out_data2', 'filter', 'filter_prev'));
    }

    /**
     * Init Master Account array container
     */
    function initMasterContainer($catid)
    {
        // Query Master Accounts data
        $master = MasterAccount::where('category_id', $catid)->get();
        $bucket = [];   // Master container

        foreach ($master as $key => $m) {
            $bucket[$m->id] = array(
                'id' => $m->id,
                'code' => $m->code,
                'name' => $m->name,
                'last_balance' => 0,
                'debet' => 0,
                'kredit' => 0,
                'balance' => 0
            );
        }
        return $bucket;
    }
}
