<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\TransactionSaleReturDataTable;
use App\Models\TransactionSale;
use App\Models\MasterAccount;
use App\Helpers\AuthHelper;
use App\Http\Requests\TransactionSaleRequest;

class TransSaleReturController extends Controller
{
    /**
     * Display a list of the Trans In.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionSaleReturDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('transactions-sale-retur.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('trans.sale.retur.create').'" class="btn btn-sm btn-primary" role="button">Add Trans</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acc_from = MasterAccount::where('id', 32)->pluck('name', 'id');
        $acc_to = MasterAccount::where('category_id', 1)->pluck('name', 'id');

        return view('trans-sale-retur.form', compact('acc_from', 'acc_to'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionSaleRequest $request)
    {
        $auth_user = AuthHelper::authSession();
        $req = $request->all();
        $data = [
            "trans_date" => $req['trans_date'],
            "receive_from" => 32,
            "store_to" => $req['store_to'],
            "value" => !empty($req['value']) ? str_replace(",", "", $req['value']) : 0,
            "sale_id" => 0,
            "reference" => $req['reference'],
            "description" => $req['description'],
            "createby" => $auth_user->id,
            "updateby" => $auth_user->id
        ];
        $trans = TransactionSale::create($data);
        // insert discount transaction
        $disc = !empty($req['disc']) ? str_replace(",", "", $req['disc']) : 0;
        $data_disc = [
            "trans_date" => $req['trans_date'],
            "receive_from" => $req['store_to'],
            "store_to" => 31,
            "value" => $disc,
            "sale_id" => $trans->id,
            "reference" => $req['reference'],
            "description" => $req['description'],
            "createby" => $auth_user->id,
            "updateby" => $auth_user->id
        ];
        $trans_disc = TransactionSale::create($data_disc);

        return redirect()->route('trans.sale.retur.index')->withSuccess(__('message.msg_added',['name' => __('transactions-sale-retur.title')]));
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
        // return view('trans-sale-retur.view', compact('data'));
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
        $data_disc = TransactionSale::with('receiveFrom')->with('storeTo')->where('sale_id', $data->id)->first();

        $acc_from = MasterAccount::where('id', 32)->pluck('name', 'id');
        $acc_to = MasterAccount::where('category_id', 1)->pluck('name', 'id');

        return view('trans-sale-retur.form', compact('data', 'data_disc', 'id', 'acc_from', 'acc_to'));
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
        $auth_user = AuthHelper::authSession();
        $trans = TransactionSale::with('receiveFrom')->with('storeTo')->findOrFail($id);
        $trans_disc = TransactionSale::with('receiveFrom')->with('storeTo')->where('sale_id', $trans->id)->first();

        // Update trans sale...
        $req = $request->all();
        $trans->trans_date = $req['trans_date'];
        $trans->receive_from = $req['receive_from'];
        $trans->store_to = 32;
        $trans->value = !empty($req['value']) ? str_replace(",", "", $req['value']) : 0;
        $trans->reference = $req['reference'];
        $trans->description = $req['description'];
        $trans->updateby = $auth_user->id;
        $trans->save();

        // Update trans disc
        $trans_disc->trans_date = $req['trans_date'];
        $trans_disc->receive_from = 31;
        $trans_disc->store_to = $req['receive_from'];
        $trans_disc->value = !empty($req['disc']) ? str_replace(",", "", $req['disc']) : 0;
        $trans_disc->reference = $req['reference'];
        $trans_disc->description = $req['description'];
        $trans_disc->updateby = $auth_user->id;
        $trans_disc->save();

        if(auth()->check()){
            return redirect()->route('trans.sale.retur.index')->withSuccess(__('message.msg_updated',['name' => __('transactions-sale-retur.title')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('transactions-sale-retur.title')]));
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
        $trans_disc = TransactionSale::where('sale_id', $trans->id)->first();
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('transsactions-sale-retur.title')]);

        if ($trans != '') {
            $trans->delete();
            $trans_disc->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('transactions-sale-retur.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }
}
