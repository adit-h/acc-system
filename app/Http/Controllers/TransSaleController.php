<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\TransactionSaleDataTable;
use App\Models\TransactionSale;
use App\Models\MasterAccount;
use App\Helpers\AuthHelper;
use App\Http\Requests\TransactionSaleRequest;

class TransSaleController extends Controller
{
    /**
     * Display a list of the Trans In.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionSaleDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('transactions-sale.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('trans.sale.create').'" class="btn btn-sm btn-primary" role="button">Add Trans</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acc_from = MasterAccount::where('category_id', 1)->pluck('name', 'id');
        $acc_to = MasterAccount::where('id', 30)->pluck('name', 'id');

        return view('trans-sale.form', compact('acc_from', 'acc_to'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionSaleRequest $request)
    {
        $req = $request->all();
        $data = [
            "trans_date" => $req['trans_date'],
            "receive_from" => $req['receive_from'],
            "store_to" => 30,
            "value" => !empty($req['value']) ? $req['value'] : 0,
            "sale_id" => 0,
            "reference" => $req['reference'],
            "description" => $req['description']
        ];
        $trans = TransactionSale::create($data);
        // insert discount transaction
        $disc = !empty($req['disc']) ? $req['disc'] : 0;
        $data_disc = [
            "trans_date" => $req['trans_date'],
            "receive_from" => 31,
            "store_to" => $req['receive_from'],
            "value" => $disc,
            "sale_id" => $trans->id,
            "reference" => $req['reference'],
            "description" => ''
        ];
        $trans_disc = TransactionSale::create($data_disc);

        return redirect()->route('trans.sale.index')->withSuccess(__('message.msg_added',['name' => __('transactions-sale.title')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = TransactionSale::with('receiveFrom')->with('storeTo')->findOrFail($id);
        // TODO : create view blade
        // return view('trans-sale.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = TransactionSale::with('receiveFrom')->with('storeTo')->findOrFail($id);

        $acc_from = MasterAccount::get()->pluck('name', 'id');
        $acc_to = MasterAccount::get()->pluck('name', 'id');

        return view('trans-sale.form', compact('data', 'id', 'acc_from', 'acc_to'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionSaleRequest $request, $id)
    {
        // dd($request->all());
        $trans = TransactionSale::with('receiveFrom')->with('storeTo')->findOrFail($id);

        // Update master account data...
        $trans->fill($request->all())->update();

        if(auth()->check()){
            return redirect()->route('trans.sale.index')->withSuccess(__('message.msg_updated',['name' => __('transactions-sale.title')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('transactions-sale.title')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trans = TransactionSale::findOrFail($id);
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('transsactions-sale.title')]);

        if ($trans != '') {
            $trans->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('transactions-sale.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }
}