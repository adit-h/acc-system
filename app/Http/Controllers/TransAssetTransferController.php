<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\TransactionAssetTransferDataTable;
use App\Models\TransactionIn;
use App\Models\MasterAccount;
use App\Helpers\AuthHelper;
use App\Http\Requests\TransactionInRequest;

class TransAssetTransferController extends Controller
{
    /**
     * Display a list of the Trans In.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionAssetTransferDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title',['form' => trans('transactions-asset-transfer.title')] );
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="'.route('trans.asset.transfer.create').'" class="btn btn-sm btn-primary" role="button">Add Trans</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acc_from = MasterAccount::whereIn('category_id', [1, 2, 3, 4, 5])->pluck('name', 'id');
        $acc_to = MasterAccount::whereIn('category_id', [1, 2, 3, 4, 5])->pluck('name', 'id');

        return view('trans-asset-transfer.form', compact('acc_from', 'acc_to'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionInRequest $request)
    {
        $auth_user = AuthHelper::authSession();
        $data = $request->all();

        $data['value'] = str_replace(",", "", $data['value']);
        $data['createby'] = $auth_user->id;
        $data['updateby'] = $auth_user->id;
        $trans = TransactionIn::create($data);

        return redirect()->route('trans.asset.transfer.index')->withSuccess(__('message.msg_added',['name' => __('transactions-asset-transfer.title')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = TransactionIn::with('receiveFrom')->with('storeTo')->findOrFail($id);
        // TODO : create view blade
        // return view('trans-asset-transfer.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = TransactionIn::with('receiveFrom')->with('storeTo')->findOrFail($id);

        $acc_from = MasterAccount::whereIn('category_id', [1, 2, 3, 4, 5])->pluck('name', 'id');
        $acc_to = MasterAccount::whereIn('category_id', [1, 2, 3, 4, 5])->pluck('name', 'id');

        return view('trans-asset-transfer.form', compact('data', 'id', 'acc_from', 'acc_to'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionInRequest $request, $id)
    {
        $auth_user = AuthHelper::authSession();
        $data = $request->all();
        $trans = TransactionIn::with('receiveFrom')->with('storeTo')->findOrFail($id);

        $data['value'] = str_replace(",", "", $data['value']);
        $data['updateby'] = $auth_user->id;
        $trans->fill($data)->update();

        if(auth()->check()){
            return redirect()->route('trans.asset.transfer.index')->withSuccess(__('message.msg_updated',['name' => __('transactions-asset-transfer.title')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated',['name' => __('transactions-asset-transfer.title')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trans = TransactionIn::findOrFail($id);
        $status = 'errors';
        $message= __('global-message.delete_form', ['form' => __('transactions-asset-transfer.title')]);

        if ($trans != '') {
            $trans->delete();
            $status = 'success';
            $message= __('global-message.delete_form', ['form' => __('transactions-asset-transfer.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }
}
