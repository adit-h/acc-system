<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\TransactionIn;
use App\Models\MasterAccount;
//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportIncomeExport implements FromView, WithColumnWidths, WithStyles
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
        ];
    }

    public function view(): View
    {
        $in_cat = 6;
        $out_cat = 8;

        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));
        if ($this->month > 1) {
            $prev_month = $this->month - 1;
            $prev_year = $this->year;
        } else {
            $prev_month = 12;
            $prev_year = $this->year - 1;
        }

        // Income
        $trans_in = DB::table('transaction_in AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->join('account_categories AS ac', 'maf.category_id', 'ac.id')
            ->where('ac.id', '=', $in_cat)
            ->whereYear('t.trans_date', '=', $this->year)
            ->whereMonth('t.trans_date', '=', $this->month)
            ->get();

        // Main Price
        // $main_price = DB::table('transaction_in AS t')
        //     ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
        //     ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
        //     ->join('master_accounts AS mat', 'mat.id', 't.store_to')
        //     ->join('account_categories AS ac', 'maf.category_id', 'ac.id')
        //     ->where('ac.id', '=', 7)
        //     ->whereYear('t.trans_date', '=', $this->year)
        //     ->whereMonth('t.trans_date', '=', $this->month)
        //     ->get();

        // Outcome
        $trans_out = DB::table('transaction_out AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->join('account_categories AS ac', 'maf.category_id', 'ac.id')
            ->where('ac.id', '=', $out_cat)
            ->whereYear('t.trans_date', '=', $this->year)
            ->whereMonth('t.trans_date', '=', $this->month)
            ->get();

        // prev month
        $filter_prev = date('F Y', strtotime($prev_year.'-'.$prev_month.'-01'));
        $trans_in_prev = DB::table('transaction_in AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->join('account_categories AS ac', 'maf.category_id', 'ac.id')
            ->where('ac.id', '=', $in_cat)
            ->whereYear('t.trans_date', '=', $prev_year)
            ->whereMonth('t.trans_date', '=', $prev_month)
            ->get();

        $trans_out_prev = DB::table('transaction_out AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->join('account_categories AS ac', 'maf.category_id', 'ac.id')
            ->where('ac.id', '=', $out_cat)
            ->whereYear('t.trans_date', '=', $prev_year)
            ->whereMonth('t.trans_date', '=', $prev_month)
            ->orderBy('trans_date')
            ->get();

        // income data. filter master account with account id = 6
        $in_data = $this->initMasterContainer($in_cat);
        foreach ($trans_in_prev as $key => $t) {
            $in_data[$t->fromId]['last_balance'] += $t->value;
        }
        foreach ($trans_in as $key => $t) {
            $in_data[$t->fromId]['balance'] += $t->value;
        }

        // outcome data. filter master account with account id = 8
        $out_data = $this->initMasterContainer($out_cat);
        foreach ($trans_out_prev as $key => $t) {
            $out_data[$t->fromId]['last_balance'] += $t->value;
        }
        foreach ($trans_out as $key => $t) {
            $out_data[$t->fromId]['balance'] += $t->value;
        }

        return view('reports.export-incomeState', compact('in_data', 'out_data', 'filter', 'filter_prev'));
    }

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
                'balance' => 0
            );
        }
        return $bucket;
    }
}
