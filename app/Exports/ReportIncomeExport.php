<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\TransactionIn;
use App\Models\MasterAccount;
use App\Models\Report;
//use Maatwebsite\Excel\Concerns\FromQuery;
//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportIncomeExport implements FromView, WithColumnWidths, WithStyles, WithColumnFormatting
{
    //use Exportable;
    protected $month, $year;
    protected $reportModel;

    public function __construct(int $m, int $y)
    {
        $this->month = $m;
        $this->year = $y;

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
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function view(): View
    {
        $in_cat = [6, 7];
        $out_cat = [8, 9];

        $filter = date('F Y', strtotime($this->year.'-'.$this->month.'-01'));
        if ($this->month > 1) {
            $prev_month = $this->month - 1;
            $prev_year = $this->year;
        } else {
            $prev_month = 12;
            $prev_year = $this->year - 1;
        }

        $date = date('Y-m-d', strtotime($this->year . '-' . $this->month . '-01'));
        $filter_prev = date('F Y', strtotime($prev_year . '-' . $prev_month . '-01'));
        $prev_date = date('Y-m-t', strtotime($prev_year . '-' . $prev_month . '-01'));

        /*
        // Income
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
        */

        // data of current month general ledger
        $data_cur = $this->reportModel->generalLedgerTemplate($date);
        $trans = $data_cur['trans'];
        $trans_prev = $data_cur['trans_prev'];

        // data of previous month general ledger
        $data_prev = $this->reportModel->generalLedgerTemplate($prev_date);
        $trans_last_month = $data_prev['trans'];
        $trans_prev_last_month = $data_prev['trans_prev'];

        // initiate data bucket
        $bucket_prev = $bucket_last_month = $bucket_prev_last_month = $this->initMasterContainer();
        // calculate previous month transactions
        foreach ($trans_prev as $key => $t) {
            $bucket_prev[$t->fromId]['debet'] += $t->value;
            $bucket_prev[$t->toId]['kredit'] += $t->value;
        }

        foreach ($trans_last_month as $key => $t) {
            $bucket_last_month[$t->fromId]['debet'] += $t->value;
            $bucket_last_month[$t->toId]['kredit'] += $t->value;
        }
        foreach ($trans_prev_last_month as $key => $t) {
            $bucket_prev_last_month[$t->fromId]['debet'] += $t->value;
            $bucket_prev_last_month[$t->toId]['kredit'] += $t->value;
        }

        // income data. filter master account with account id = 6
        // switch debet & kredit position
        $in_data1 = $this->initMasterContainer(6);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in_data1)) {
                $in_data1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] + $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data1)) {
                if ($t->fromId == 31) {
                    $in_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['kredit'] - $bucket_prev[$t->fromId]['debet'];
                } else {
                    $in_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
                }
            }
            if (array_key_exists($t->toId, $in_data1) && array_key_exists($t->fromId, $in_data1))  {
                //$in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['last_balance'] + $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in_data1)) {
                //$in_data1[$t->toId]['balance'] += $t->value;
                $in_data1[$t->toId]['debet'] += $t->value;
                $in_data1[$t->toId]['balance'] = $in_data1[$t->toId]['debet'] - $in_data1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data1)) {
                $in_data1[$t->fromId]['kredit'] += $t->value;
                $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data1) && array_key_exists($t->fromId, $in_data1))  {
                $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
            }
        }
        // income data. filter master account with account id = 7
        $in_data2 = $this->initMasterContainer(7);
        $supp = MasterAccount::where('code', "2000")->pluck('id');
        $supp_id = $supp[0];
        $begin_supp_id = 33;
        $purchase_id = 34;
        $last_supp_id = 35;

        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $in_data2)) {
                $in_data2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data2)) {
                $in_data2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data2) && array_key_exists($t->fromId, $in_data2))  {
                //$in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['last_balance'] + $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }

            // lets count persediaan awal
            if (array_key_exists($supp_id, $bucket_prev)) {
                $in_data2[$begin_supp_id]['last_balance'] = $bucket_prev[$supp_id]['last_balance'];
            }
            // handle pembelian bersih total
            if ($t->toId == $purchase_id || $t->fromId == $purchase_id) {
                $in_data2[$purchase_id]['last_balance'] = $bucket_prev[$purchase_id]['debet'];
            }
        }

        foreach ($trans_last_month as $key => $t) {
            if (array_key_exists($t->toId, $in_data2)) {
                $in_data2[$t->toId]['last_balance'] = $bucket_last_month[$t->toId]['debet'] - $bucket_last_month[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data2)) {
                $in_data2[$t->fromId]['last_balance'] = $bucket_last_month[$t->fromId]['debet'] - $bucket_last_month[$t->fromId]['kredit'];
            }
            // handle pembelian bersih total
            if ($t->toId == $purchase_id || $t->fromId == $purchase_id) {
                $in_data2[$purchase_id]['last_balance'] = $bucket_last_month[$purchase_id]['debet'];
            }
        }

        $deb_supp_prev = $cred_supp_prev = 0;
        foreach ($trans_prev_last_month as $key => $t) {
            $bucket_prev_last_month[$t->fromId]['last_balance'] = $bucket_prev_last_month[$t->fromId]['debet'] - $bucket_prev_last_month[$t->fromId]['kredit'];
            $bucket_prev_last_month[$t->toId]['last_balance'] = $bucket_prev_last_month[$t->toId]['debet'] - $bucket_prev_last_month[$t->toId]['kredit'];
            // lets count persediaan awal
            if (array_key_exists($supp_id, $bucket_prev_last_month)) {
                $in_data2[$begin_supp_id]['last_balance'] = $bucket_prev_last_month[$supp_id]['last_balance'];
            }
            // handle pembelian bersih total - not used
            if ($t->toId == $purchase_id || $t->fromId == $purchase_id) {
                //$in_data2[$purchase_id]['balance'] = $in_data2[$purchase_id]['debet'];
            }

            // handle count persediaan akhir
            if ($t->toId == $supp_id) {
                $cred_supp_prev += $t->value;
            }
            if ($t->fromId == $supp_id) {
                $deb_supp_prev += $t->value;
            }
            if ($t->toId == $supp_id || $t->fromId == $supp_id) {
                //$in_data2[$last_supp_id]['last_balance'] = $deb_supp_prev - $cred_supp_prev;
            }
        }

        $deb_supp = $cred_supp = 0;
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $in_data2)) {
                //$in_data2[$t->toId]['balance'] += $t->value;
                $in_data2[$t->toId]['kredit'] += $t->value;
                $in_data2[$t->toId]['balance'] = $in_data2[$t->toId]['debet'] - $in_data2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $in_data2)) {
                $in_data2[$t->fromId]['debet'] += $t->value;
                $in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $in_data2) && array_key_exists($t->fromId, $in_data2))  {
                //$in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
            }

            // lets count persediaan awal
            if (array_key_exists($begin_supp_id, $in_data2)) {
                $in_data2[$begin_supp_id]['balance'] = $bucket_prev[$supp_id]['debet'] - $bucket_prev[$supp_id]['kredit'];
                $in_data2[$last_supp_id]['last_balance'] = $in_data2[$begin_supp_id]['balance'];
            }
            // handle pembelian bersih total
            if ($t->toId == $purchase_id || $t->fromId == $purchase_id) {
                $in_data2[$purchase_id]['balance'] = $in_data2[$purchase_id]['debet'];
            }
            // handle count persediaan akhir
            if ($t->toId == $supp_id) {
                $cred_supp += $t->value;
            }
            if ($t->fromId == $supp_id) {
                $deb_supp += $t->value;
            }
            if ($t->toId == $supp_id || $t->fromId == $supp_id) {
                $in_data2[$last_supp_id]['balance'] = $in_data2[$last_supp_id]['last_balance'] + $deb_supp - $cred_supp;
            }
        }

        // outcome data. filter master account with account id = 8
        $out_data1 = $this->initMasterContainer(8);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $out_data1)) {
                $out_data1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data1)) {
                $out_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data1) && array_key_exists($t->fromId, $out_data1))  {
                //$out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['last_balance'] + $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $out_data1)) {
                //$out_data1[$t->toId]['balance'] += $t->value;
                $out_data1[$t->toId]['kredit'] += $t->value;
                $out_data1[$t->toId]['balance'] = $out_data1[$t->toId]['debet'] - $out_data1[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data1)) {
                $out_data1[$t->fromId]['debet'] += $t->value;
                $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data1) && array_key_exists($t->fromId, $out_data1))  {
                $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
            }
        }
        // outcome data. filter master account with account id = 9
        $out_data2 = $this->initMasterContainer(9);
        foreach ($trans_prev as $key => $t) {
            if (array_key_exists($t->toId, $out_data2)) {
                $out_data2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data2)) {
                $out_data2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data2) && array_key_exists($t->fromId, $out_data2))  {
                //$out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['last_balance'] + $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
        }
        foreach ($trans as $key => $t) {
            if (array_key_exists($t->toId, $out_data2)) {
                //$out_data2[$t->toId]['balance'] += $t->value;
                $out_data2[$t->toId]['kredit'] += $t->value;
                $out_data2[$t->toId]['balance'] = $out_data2[$t->toId]['debet'] - $out_data2[$t->toId]['kredit'];
            }
            if (array_key_exists($t->fromId, $out_data2)) {
                $out_data2[$t->fromId]['debet'] += $t->value;
                $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
            if (array_key_exists($t->toId, $out_data2) && array_key_exists($t->fromId, $out_data2))  {
                $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
            }
        }

        return view('reports.export-incomeState', compact('in_data1', 'in_data2', 'out_data1', 'out_data2', 'filter', 'filter_prev',));
    }

    function initMasterContainer($catid = null)
    {
        // Query Master Accounts data
        $master = MasterAccount::get();
        if ($catid > 0) {
            $master = MasterAccount::where('category_id', $catid)->get();
        }
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
