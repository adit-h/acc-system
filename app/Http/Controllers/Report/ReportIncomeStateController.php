<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\DataTables\Report\ReportIncomeStateDataTable;

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
        $trans_in = DB::table('transaction_in')
            ->select(DB::raw('master_accounts.id, master_accounts.code, master_accounts.name, SUM(transaction_in.value) AS value1'))
            ->join('master_accounts', 'master_accounts.id', 'transaction_in.store_to')
            ->groupBy('master_accounts.id', 'master_accounts.code', 'master_accounts.name')
            ->get();
        $trans_out = DB::table('transaction_out')
            ->select(DB::raw('master_accounts.id, master_accounts.code, master_accounts.name, SUM(transaction_out.value) AS value1'))
            ->join('master_accounts', 'master_accounts.id', 'transaction_out.store_to')
            ->groupBy('master_accounts.id', 'master_accounts.code', 'master_accounts.name')
            ->get();

        return view('report-incomeState.list', compact('trans_in', 'trans_out'));
    }

    /**
     * Filter report by date
     *
     * @return
     */
    public function filter(Request $request)
    {
        $date = $request->date_input;
        $trans_in = $trans_out = [];    // init

        if (!empty($date)) {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));

            $trans_in = DB::table('transaction_in')
                ->select(DB::raw('master_accounts.id, master_accounts.code, master_accounts.name, SUM(transaction_in.value) AS value1'))
                ->join('master_accounts', 'master_accounts.id', 'transaction_in.store_to')
                ->whereYear('transaction_in.trans_date', '=', $year)
                ->whereMonth('transaction_in.trans_date', '=', $month)
                ->groupBy('master_accounts.id', 'master_accounts.code', 'master_accounts.name')
                ->get();
            $trans_out = DB::table('transaction_out')
                ->select(DB::raw('master_accounts.id, master_accounts.code, master_accounts.name, SUM(transaction_out.value) AS value1'))
                ->join('master_accounts', 'master_accounts.id', 'transaction_out.store_to')
                ->whereYear('transaction_out.trans_date', '=', $year)
                ->whereMonth('transaction_out.trans_date', '=', $month)
                ->groupBy('master_accounts.id', 'master_accounts.code', 'master_accounts.name')
                ->get();
        }

        return view('report-incomeState.list', compact('trans_in', 'trans_out'));
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
}
