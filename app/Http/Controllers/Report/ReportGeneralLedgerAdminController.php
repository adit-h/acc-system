<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\ReportGeneralLedgerAdminExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\MasterAccount;
use App\Helpers\AuthHelper;

class ReportGeneralLedgerAdminController extends Controller
{
    protected $reportModel;
    public function __construct()
    {
        $this->reportModel = new Report();
    }

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

        return view('report-generalLedger-adm.list', compact('trans'));
    }

    /**
     * Filter report by date
     *
     * @return
     */
    public function filter(Request $request)
    {
        $date = $request->date_input;
        $trans = $trans_prev = $master = [];    // init
        $bucket = $bucket_prev = [];
        $filter = '';

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

            // Query current Month Transactions
            $filter = date('F Y', strtotime($year . '-' . $month . '-01'));
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

            // Query all trans to previous Month
            $filter_prev = date('F Y', strtotime($prev_year . '-' . $prev_month . '-01'));
            $prev_date = date('Y-m-t', strtotime($prev_year . '-' . $prev_month . '-01'));
            $trans_in_prev = DB::table('transaction_in AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->where('t.trans_date', '<=', $prev_date);
            $trans_sale_prev = DB::table('transaction_sale AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->where('t.trans_date', '<=', $prev_date);
            $trans_prev = DB::table('transaction_out AS t')
                ->select(DB::raw('t.id, t.trans_date, maf.id AS fromId, maf.code AS fromCode, maf.name AS fromName,
                    maf.category_id AS fromCat, mat.id AS toId, mat.code AS toCode, mat.name AS toName, mat.category_id AS toCat,
                    t.value, t.reference, t.description'))
                ->join('master_accounts AS maf', 'maf.id', 't.receive_from')
                ->join('master_accounts AS mat', 'mat.id', 't.store_to')
                ->where('t.trans_date', '<=', $prev_date)
                ->union($trans_in_prev)
                ->union($trans_sale_prev)
                ->orderBy('trans_date')
                ->get();

            //TODO : Add filter account
            // akun yg di income statement dulu aja
            // penjualan dan biaya !!!

            //$trans_prev = $trans_prev_raw->get();
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
                } else if ($bucket[$t->fromId]['catid'] == 4 || $bucket[$t->fromId]['catid'] == 5 || $bucket[$t->fromId]['catid'] == 6) {
                    $bucket[$t->fromId]['last_balance'] = $bucket_prev[$t->fromId]['kredit'] - $bucket_prev[$t->fromId]['debet'];
                }
            }
            // lets do for All transaction
            foreach ($trans as $key => $t) {
                // switch debet/credit position
                //dump($t, $bucket[$t->toId]['catid'], $bucket[$t->fromId]['catid']);
                if ($bucket[$t->toId]['catid'] == 4 || $bucket[$t->toId]['catid'] == 5 || $bucket[$t->toId]['catid'] == 6) {
                    $bucket[$t->fromId]['debet'] += $t->value;
                    $bucket[$t->toId]['debet'] += $t->value;
                } else if ($bucket[$t->fromId]['catid'] == 4 || $bucket[$t->fromId]['catid'] == 5 || $bucket[$t->fromId]['catid'] == 6) {
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
        }

        return view('report-generalLedger-adm.list', compact('bucket', 'catid', 'ex_code', 'trans', 'filter', 'date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

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
        $filename = 'report_gl_adm_' . $month . $year . '.xlsx';

        return Excel::download(new ReportGeneralLedgerAdminExport($month, $year), $filename);
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
        $filename = 'report_gl_adm_' . $month . $year . '.pdf';

        // using fromQuery
        // return (new ReportGeneralLedgerAdminExport($month, $year))->download($filename, \Maatwebsite\Excel\Excel::DOMPDF);
        return Excel::download(new ReportGeneralLedgerAdminExport($month, $year), $filename);
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
        $filename = 'report_gl_adm_' . $month . $year . '.html';

        //using formQuery
        //return (new ReportGeneralLedgerAdminExport($month, $year))->download($filename, \Maatwebsite\Excel\Excel::HTML);
        return Excel::download(new ReportGeneralLedgerAdminExport($month, $year), $filename);
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
            if (array_key_exists($t->toId, $in1) && array_key_exists($t->fromId, $in1)) {
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
            if (array_key_exists($t->toId, $in2) && array_key_exists($t->fromId, $in2)) {
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
            if (array_key_exists($t->toId, $out1) && array_key_exists($t->fromId, $out1)) {
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
            if (array_key_exists($t->toId, $out2) && array_key_exists($t->fromId, $out2)) {
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
