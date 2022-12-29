<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\DataTables\Report\ReportIncomeStateDataTable;

use App\Exports\ReportBalanceSheetExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use App\Models\TransactionIn;
use App\Models\MasterAccount;
use App\Models\AccountCategory;
use App\Helpers\AuthHelper;

class ReportBalanceSheetController extends Controller
{
    /**
     * Display a list of the Master Account.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $trans_in = DB::table('transaction_in AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to');
        $trans = DB::table('transaction_out AS t')
            ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
            ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
            ->join('master_accounts AS mat', 'mat.id', 't.store_to')
            ->union($trans_in)
            ->orderBy('trans_date')
            ->get();

        return view('report-balanceSheet.list', compact('trans'));
    }

    /**
     * Filter report by date
     *
     * @return
     */
    public function filter(Request $request)
    {
        $date = $request->date_input;
        $trans_in = $trans_out = $main_price = [];
        $trans_in_prev = $trans_out_prev = $master = [];    // init
        $in_data1 = $in_data2 = $in_data3 = [];
        $out_data1 = $out_data2 = [];
        $bucket = $bucket_prev = [];
        $filter = $filter_prev = '';
        $in_cat = [1, 2, 3];
        $out_cat = [4, 5];

        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            if ($month > 1) {
                $prev_month = $month - 1;
                $prev_year = $year;
            } else {
                $prev_month = 12;
                $prev_year = $year - 1;
            }

            $filter = date('F Y', strtotime($year.'-'.$month.'-01'));
            $trans_in = DB::table('transaction_in AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month);
            $trans_sale = DB::table('transaction_sale AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month);
            $trans = DB::table('transaction_out AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month)
                ->union($trans_sale)
                ->union($trans_in)
                ->orderBy('trans_date')
                ->get();

            // prev month
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
                    //$in_data1[$t->toId]['balance'] = $in_data1[$t->toId]['last_balance'] +  $in_data1[$t->toId]['debet'] - $in_data1[$t->toId]['kredit'];
                    $in_data1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
                }
                if (array_key_exists($t->fromId, $in_data1)) {
                    $in_data1[$t->fromId]['debet'] += $t->value;
                    //$in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['last_balance'] + $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
                    $in_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
                }
                if (array_key_exists($t->toId, $in_data1) && array_key_exists($t->fromId, $in_data1))  {
                    //$in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['last_balance'] + $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
                }
            }
            foreach ($trans as $key => $t) {
                if (array_key_exists($t->toId, $in_data1)) {
                    //$in_data1[$t->toId]['balance'] += $t->value;
                    $in_data1[$t->toId]['kredit'] += $t->value;
                    $in_data1[$t->toId]['balance'] = $in_data1[$t->toId]['debet'] - $in_data1[$t->toId]['kredit'];
                }
                if (array_key_exists($t->fromId, $in_data1)) {
                    $in_data1[$t->fromId]['debet'] += $t->value;
                    $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
                }
                if (array_key_exists($t->toId, $in_data1) && array_key_exists($t->fromId, $in_data1))  {
                    $in_data1[$t->fromId]['balance'] = $in_data1[$t->fromId]['debet'] - $in_data1[$t->fromId]['kredit'];
                }
            }

            // Aktiva 2. filter master account with account id = 2
            $in_data2 = $this->initMasterContainer(2);
            foreach ($trans_prev as $key => $t) {
                if (array_key_exists($t->toId, $in_data2)) {
                    //$in_data2[$t->toId]['balance'] += $t->value;
                    $in_data2[$t->toId]['kredit'] += $t->value;
                    //$in_data2[$t->toId]['balance'] = $in_data2[$t->toId]['last_balance'] + $in_data2[$t->toId]['debet'] - $in_data2[$t->toId]['kredit'];
                    $in_data2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
                }
                if (array_key_exists($t->fromId, $in_data2)) {
                    $in_data2[$t->fromId]['debet'] += $t->value;
                    //$in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['last_balance'] + $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
                    $in_data2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
                }
                if (array_key_exists($t->toId, $in_data2) && array_key_exists($t->fromId, $in_data2))  {
                    //$in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['last_balance'] + $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
                }
            }
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
                    $in_data2[$t->fromId]['balance'] = $in_data2[$t->fromId]['debet'] - $in_data2[$t->fromId]['kredit'];
                }
            }
            // Aktiva 3. filter master account with account id = 3
            $in_data3 = $this->initMasterContainer(3);
            foreach ($trans_prev as $key => $t) {
                if (array_key_exists($t->toId, $in_data3)) {
                    //$in_data3[$t->toId]['balance'] += $t->value;
                    $in_data3[$t->toId]['kredit'] += $t->value;
                    //$in_data3[$t->toId]['balance'] = $in_data3[$t->toId]['last_balance'] + $in_data3[$t->toId]['debet'] - $in_data3[$t->toId]['kredit'];
                    $in_data3[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
                }
                if (array_key_exists($t->fromId, $in_data3)) {
                    $in_data3[$t->fromId]['debet'] += $t->value;
                    //$in_data3[$t->fromId]['balance'] = $in_data3[$t->fromId]['last_balance'] + $in_data3[$t->fromId]['debet'] - $in_data3[$t->fromId]['kredit'];
                    $in_data3[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
                }
                if (array_key_exists($t->toId, $in_data3) && array_key_exists($t->fromId, $in_data3))  {
                    //$in_data3[$t->fromId]['balance'] = $in_data3[$t->fromId]['last_balance'] + $in_data3[$t->fromId]['debet'] - $in_data3[$t->fromId]['kredit'];
                }
            }
            foreach ($trans as $key => $t) {
                if (array_key_exists($t->toId, $in_data3)) {
                    //$in_data3[$t->toId]['balance'] += $t->value;
                    $in_data3[$t->toId]['kredit'] += $t->value;
                    $in_data3[$t->toId]['balance'] = $in_data3[$t->toId]['debet'] - $in_data3[$t->toId]['kredit'];
                }
                if (array_key_exists($t->fromId, $in_data3)) {
                    $in_data3[$t->fromId]['debet'] += $t->value;
                    $in_data3[$t->fromId]['balance'] = $in_data3[$t->fromId]['debet'] - $in_data3[$t->fromId]['kredit'];
                }
                if (array_key_exists($t->toId, $in_data3) && array_key_exists($t->fromId, $in_data3))  {
                    $in_data3[$t->fromId]['balance'] = $in_data3[$t->fromId]['debet'] - $in_data3[$t->fromId]['kredit'];
                }
            }

            // Pasiva 1. filter master account with account id = 4
            $out_data1 = $this->initMasterContainer(4);
            foreach ($trans_prev as $key => $t) {
                if (array_key_exists($t->toId, $out_data1)) {
                    $out_data1[$t->toId]['debet'] += $t->value;
                    //$out_data1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
                    // switch debet/credit position
                    $out_data1[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['kredit'] - $bucket_prev[$t->toId]['debet'];
                }
                if (array_key_exists($t->fromId, $out_data1)) {
                    $out_data1[$t->fromId]['kredit'] += $t->value;
                    //$out_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
                    // switch debet/credit position
                    $out_data1[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['kredit'] - $bucket_prev[$t->fromId]['debet'];
                }
            }
            foreach ($trans as $key => $t) {
                if (array_key_exists($t->toId, $out_data1)) {
                    //$out_data1[$t->toId]['balance'] += $t->value;
                    $out_data1[$t->toId]['debet'] += $t->value;
                    $out_data1[$t->toId]['balance'] = $out_data1[$t->toId]['debet'] - $out_data1[$t->toId]['kredit'];
                }
                if (array_key_exists($t->fromId, $out_data1)) {
                    $out_data1[$t->fromId]['kredit'] += $t->value;
                    $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
                }
                if (array_key_exists($t->toId, $out_data1) && array_key_exists($t->fromId, $out_data1))  {
                    $out_data1[$t->fromId]['balance'] = $out_data1[$t->fromId]['debet'] - $out_data1[$t->fromId]['kredit'];
                }
            }

            // Pasiva 2. filter master account with account id = 5
            $out_data2 = $this->initMasterContainer(5);
            foreach ($trans_prev as $key => $t) {
                if (array_key_exists($t->toId, $out_data2)) {
                    //$out_data2[$t->toId]['balance'] += $t->value;
                    $out_data2[$t->toId]['debet'] += $t->value;
                    //$out_data2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['debet'] - $bucket_prev[$t->toId]['kredit'];
                    // switch debet/credit position
                    $out_data2[$t->toId]['last_balance'] = $bucket_prev[$t->toId]['kredit'] - $bucket_prev[$t->toId]['debet'];
                }
                if (array_key_exists($t->fromId, $out_data2)) {
                    $out_data2[$t->fromId]['kredit'] += $t->value;
                    //$out_data2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['debet'] - $bucket_prev[$t->fromId]['kredit'];
                    // switch debet/credit position
                    $out_data2[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['kredit'] - $bucket_prev[$t->fromId]['debet'];
                }
            }
            foreach ($trans as $key => $t) {
                if (array_key_exists($t->toId, $out_data2)) {
                    //$out_data2[$t->toId]['balance'] += $t->value;
                    $out_data2[$t->toId]['debet'] += $t->value;
                    $out_data2[$t->toId]['balance'] = $out_data2[$t->toId]['debet'] - $out_data2[$t->toId]['kredit'];
                }
                if (array_key_exists($t->fromId, $out_data2)) {
                    $out_data2[$t->fromId]['kredit'] += $t->value;
                    $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
                }
                if (array_key_exists($t->toId, $out_data2) && array_key_exists($t->fromId, $out_data2))  {
                    $out_data2[$t->fromId]['balance'] = $out_data2[$t->fromId]['debet'] - $out_data2[$t->fromId]['kredit'];
                }
            }

            // lets count special account
            $special = $this->countIncomeState($trans, $trans_prev, $bucket_prev);
            $out_data2[29]['debet'] = $special['debet'];
            $out_data2[29]['kredit'] = $special['kredit'];
            $out_data2[29]['balance'] = $special['balance'];
            $out_data2[29]['last_balance'] = $special['last_balance'];

        }

        return view('report-balanceSheet.list', compact('in_data1', 'in_data2', 'in_data3', 'out_data1', 'out_data2', 'filter', 'filter_prev', 'date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store() {}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {}

    public function exportExcel(Request $request)
    {
        $date = $request->date_input;
        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
        } else {
            $year = date('Y');
            $month = date('m');
        }
        $filename = 'report_balance_sheet_'.$month.$year.'.xlsx';

        return Excel::download(new ReportBalanceSheetExport($month, $year), $filename);
    }

    public function exportPdf(Request $request)
    {
        $date = $request->date_input;
        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
        } else {
            $year = date('Y');
            $month = date('m');
        }
        $filename = 'report_balance_sheet_'.$month.$year.'.pdf';

        // using fromQuery
        // return (new ReportBalanceSheetExport($month, $year))->download($filename, \Maatwebsite\Excel\Excel::DOMPDF);
        return Excel::download(new ReportBalanceSheetExport($month, $year), $filename);
    }

    public function exportHtml(Request $request)
    {
        $date = $request->date_input;
        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
        } else {
            $year = date('Y');
            $month = date('m');
        }
        $filename = 'report_balance_sheet_'.$month.$year.'.html';

        //using formQuery
        //return (new ReportBalanceSheetExport($month, $year))->download($filename, \Maatwebsite\Excel\Excel::HTML);
        return Excel::download(new ReportBalanceSheetExport($month, $year), $filename);
    }

    function countIncomeState($trans, $trans_prev, $bucket_prev)
    {
        $result = [];
        // income data. filter master account with account id = 6
        // switch debet & kredit position
        $in1 = $this->initMasterContainer(6);
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
        $in2 = $this->initMasterContainer(7);
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
        $out1 = $this->initMasterContainer(8);
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
        $out2 = $this->initMasterContainer(9);
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

    /**
     * Init Master Account array container for 2 month periods
     * @param catid Account category id
     */
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
                'catid' => $m->category_id,
                'last_balance' => 0,
                'debet' => 0,
                'kredit' => 0,
                'balance' => 0
            );
        }
        return $bucket;
    }

}
