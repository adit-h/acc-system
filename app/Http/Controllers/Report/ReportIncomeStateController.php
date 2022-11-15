<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\DataTables\Report\ReportIncomeStateDataTable;

use App\Exports\ReportIncomeExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use App\Models\TransactionIn;
use App\Models\MasterAccount;
use App\Models\AccountCategory;
use App\Helpers\AuthHelper;

class ReportIncomeStateController extends Controller
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

        return view('report-incomeState.list', compact('trans'));
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
        $bucket = $bucket_prev = [];
        $filter = '';
        $in_cat = 6;
        $out_cat = 8;

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
            // Income
            $trans_in = DB::table('transaction_in AS t')
                ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->join('account_categories AS ac', 'maf.category_id', 'ac.id')
                ->where('ac.id', '=', $in_cat)
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month)
                ->get();

            // Main Price
            $main_price = DB::table('transaction_in AS t')
                ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->join('account_categories AS ac', 'maf.category_id', 'ac.id')
                ->where('ac.id', '=', 7)
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month)
                ->get();

            // Outcome
            $trans_out = DB::table('transaction_out AS t')
                ->select(DB::raw('t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName, mat.id AS toId, mat.code AS toCode, mat.name AS toName, t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->join('account_categories AS ac', 'maf.category_id', 'ac.id')
                ->where('ac.id', '=', $out_cat)
                ->whereYear('t.trans_date', '=', $year)
                ->whereMonth('t.trans_date', '=', $month)
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

        }

        return view('report-incomeState.list', compact('in_data', 'out_data', 'filter', 'filter_prev', 'date'));
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
    public function store(MasterAccountRequest $request) {}

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
    public function update(MasterAccountRequest $request, $id) {}

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
        $filename = 'report_income_'.$month.$year.'.xlsx';

        return Excel::download(new ReportIncomeExport($month, $year), $filename);
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
        $filename = 'report_income_'.$month.$year.'.pdf';

        // using fromQuery
        // return (new ReportIncomeExport($month, $year))->download($filename, \Maatwebsite\Excel\Excel::DOMPDF);
        return Excel::download(new ReportIncomeExport($month, $year), $filename);
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
        $filename = 'report_income_'.$month.$year.'.html';

        //using formQuery
        //return (new ReportIncomeExport($month, $year))->download($filename, \Maatwebsite\Excel\Excel::HTML);
        return Excel::download(new ReportIncomeExport($month, $year), $filename);
    }

    /**
     * Init Master Account array container for 2 month periods
     * @param catid Account category id
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
                'balance' => 0
            );
        }
        return $bucket;
    }

}
