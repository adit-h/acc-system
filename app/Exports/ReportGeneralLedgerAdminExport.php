<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\MasterAccount;
use App\Models\TransactionIn;
use App\Models\Report;
//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportGeneralLedgerAdminExport implements FromView, WithColumnWidths, WithStyles
{
    //use Exportable;

    protected $reportModel;
    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year = $year;

        $this->reportModel = new Report();
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

        // Query previous Month Transactions
        $filter_prev = date('F Y', strtotime($prev_year.'-'.$prev_month.'-01'));
        $prev_date = date('Y-m-t', strtotime($prev_year.'-'.$prev_month.'-01'));
        $trans_in_prev = DB::table('transaction_in AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->where('t.trans_date','<=', $prev_date);
        $trans_sale_prev = DB::table('transaction_sale AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->where('t.trans_date','<=', $prev_date);
        $trans_prev = DB::table('transaction_out AS t')
            ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->where('t.trans_date','<=', $prev_date)
            ->union($trans_in_prev)
            ->union($trans_sale_prev)
            ->orderBy('trans_date')
            ->get();

        $catid = [1, 2, 4, 6, 7, 8, 9];     // included account category
        $ex_code = ['4003'];                // excluded account code
        $bucket = $bucket_prev = $this->reportModel->initMasterContainer();
        // calculate previous month transactions
        foreach ($trans_prev as $key => $t) {
            $bucket_prev[$t->fromId]['debet'] += $t->value;
            $bucket_prev[$t->toId]['kredit'] += $t->value;
        }

        // Add calculate previous month transactions
        foreach ($trans_prev as $key => $t) {
            $bucket[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            if ($bucket[$t->toId]['catid'] == 4 || $bucket[$t->toId]['catid'] == 5 || $bucket[$t->toId]['catid'] == 6) {
                $bucket[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['kredit'] - $bucket_prev[$t->toId]['debet'];
            }
            else if ($bucket[$t->fromId]['catid'] == 4 ||$bucket[$t->fromId]['catid'] == 5 || $bucket[$t->fromId]['catid'] == 6) {
                $bucket[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['kredit'] - $bucket_prev[$t->fromId]['debet'];
            }
        }
        // lets do for All transaction
        foreach ($trans as $key => $t) {
            // switch debet/credit position
            if ($bucket[$t->toId]['catid'] == 4 || $bucket[$t->toId]['catid'] == 5 || $bucket[$t->toId]['catid'] == 6) {
                $bucket[$t->fromId]['debet'] += $t->value;
                $bucket[$t->toId]['debet'] += $t->value;
            } else if ($bucket[$t->fromId]['catid'] == 4 ||$bucket[$t->fromId]['catid'] == 5 || $bucket[$t->fromId]['catid'] == 6) {
                $bucket[$t->fromId]['kredit'] += $t->value;
                $bucket[$t->toId]['kredit'] += $t->value;
            } else {
                $bucket[$t->fromId]['debet'] += $t->value;
                $bucket[$t->toId]['kredit'] += $t->value;
            }
        }

        // lets count special account
        // $special = $this->countIncomeState($trans, $trans_prev, $bucket_prev);
        // $bucket[29]['debet'] = $special['debet'];
        // $bucket[29]['kredit'] = $special['kredit'];
        // $bucket[29]['balance'] = $special['balance'];
        // $bucket[29]['last_balance'] = $special['last_balance'];

        return view('reports.export-generalLedger-adm', compact('bucket', 'catid', 'ex_code', 'trans', 'filter'));
    }

    function countIncomeState($trans, $trans_prev, $bucket_prev)
    {
        $result = [];
        // income data. filter master account with account id = 6
        // switch debet & kredit position
        $in1 = $this->reportModel->initMasterContainer(6);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in1)) {
                $in1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] + $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in1)) {
                if ($t->fromId == 31) {
                    $in1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['kredit'] - $bucket_prev[$t->fromId]['debet'];
                } else {
                    $in1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
                }
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in1)) {
                $in1[$t->toId]['debet'] += $t->value;
                $in1[$t->toId]['balance'] = $in1[$t->toId]['debet'] - $in1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in1)) {
                $in1[$t->fromId]['kredit'] += $t->value;
                $in1[$t->fromId]['balance'] = $in1[$t->fromId]['debet'] - $in1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in1) && array_key_exists($t->fromId, $in1))  {
                $in1[$t->fromId]['balance'] = $in1[$t->fromId]['debet'] - $in1[$t->fromId]['kredit'];
            }
        }
        // income data. filter master account with account id = 7
        $in2 = $this->reportModel->initMasterContainer(7);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in2)) {
                $in2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in2)) {
                $in2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in2)) {
                $in2[$t->toId]['kredit'] += $t->value;
                $in2[$t->toId]['balance'] = $in2[$t->toId]['debet'] - $in2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in2)) {
                $in2[$t->fromId]['debet'] += $t->value;
                $in2[$t->fromId]['balance'] = $in2[$t->fromId]['debet'] - $in2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in2) && array_key_exists($t->fromId, $in2))  {
                $in2[$t->fromId]['balance'] = $in2[$t->fromId]['debet'] - $in2[$t->fromId]['kredit'];
            }
        }

        // outcome data. filter master account with account id = 8
        $out1 = $this->reportModel->initMasterContainer(8);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $out1)) {
                $out1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out1)) {
                $out1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $out1)) {
                $out1[$t->toId]['kredit'] += $t->value;
                $out1[$t->toId]['balance'] = $out1[$t->toId]['debet'] - $out1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out1)) {
                $out1[$t->fromId]['debet'] += $t->value;
                $out1[$t->fromId]['balance'] = $out1[$t->fromId]['debet'] - $out1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out1) && array_key_exists($t->fromId, $out1))  {
                $out1[$t->fromId]['balance'] = $out1[$t->fromId]['debet'] - $out1[$t->fromId]['kredit'];
            }
        }
        // outcome data. filter master account with account id = 9
        $out2 = $this->reportModel->initMasterContainer(9);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $out2)) {
                $out2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out2)) {
                $out2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $out2)) {
                $out2[$t->toId]['kredit'] += $t->value;
                $out2[$t->toId]['balance'] = $out2[$t->toId]['debet'] - $out2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out2)) {
                $out2[$t->fromId]['debet'] += $t->value;
                $out2[$t->fromId]['balance'] = $out2[$t->fromId]['debet'] - $out2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out2) && array_key_exists($t->fromId, $out2))  {
                $out2[$t->fromId]['balance'] = $out2[$t->fromId]['debet'] - $out2[$t->fromId]['kredit'];
            }
        }

        $total6 = $total7 = $total8 = $total9 = [
            'balance' => 0,
            'last_balance' => 0,
            'debet' => 0,
            'kredit' => 0
        ];
        foreach ($in1 as $key => $t) {
            $total6['last_balance'] += $t['last_balance'];
            $total6['balance'] += $t['balance'];
            $total6['debet'] += $t['debet'];
            $total6['kredit'] += $t['kredit'];
        }
        foreach ($in2 as $key => $t) {
            $total7['last_balance'] += $t['last_balance'];
            $total7['balance'] += $t['balance'];
            $total7['debet'] += $t['debet'];
            $total7['kredit'] += $t['kredit'];
        }
        foreach ($out1 as $key => $t) {
            $total8['last_balance'] += $t['last_balance'];
            $total8['balance'] += $t['balance'];
            $total8['debet'] += $t['debet'];
            $total8['kredit'] += $t['kredit'];
        }
        foreach ($out2 as $key => $t) {
            $total9['last_balance'] += $t['last_balance'];
            $total9['balance'] += $t['balance'];
            $total9['debet'] += $t['debet'];
            $total9['kredit'] += $t['kredit'];
        }

        $result['last_balance'] = $total6['last_balance'] - ($total7['last_balance'] + $total8['last_balance'] + $total9['last_balance']);
        $result['balance'] = $total6['balance'] - ($total7['balance'] + $total8['balance'] + $total9['balance']);
        $result['debet'] = $total6['debet'] - ($total7['debet'] + $total8['debet'] + $total9['debet']);
        $result['kredit'] = $total6['kredit'] - ($total7['kredit'] + $total8['kredit'] + $total9['kredit']);

        return $result;
    }
}
